<?php
/**
 * @brief		alliance_promote Task
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	p38b2f225e3
 * @since		09 Jun 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\pluginTasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * alliance_promote Task
 */
class _alliance_promote extends \IPS\Task
{
	/**
	 * Execute
	 *
	 * If ran successfully, should return anything worth logging. Only log something
	 * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
	 * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
	 * Tasks should execute within the time of a normal HTTP request.
	 *
	 * @return	mixed	Message to log or NULL
	 * @throws	\IPS\Task\Exception
	 */
	public function execute()
	{
		$clubs = json_decode( \IPS\Settings::i()->promote_clubs );
		$club_not_delete = json_decode( \IPS\Settings::i()->not_delete_members_in_club );
		$group_alliance = \IPS\Settings::i()->group_alliance;
		$group_conseil_alliance = \IPS\Settings::i()->group_conseil_alliance;
		$group_non_membre = \IPS\Settings::i()->group_non_membre;

		$members_to_add = \IPS\Db::i()->select( 'member_id', 'core_clubs_memberships', array( array( 'status IN (?,?,?) AND club_id IN('.implode( ',', $clubs ).')', "member", "leader", "moderator" ) ) );

		for( $i = 0; $i < $members_to_add->count(); $i++ )
		{
			$members_to_add->next();
			$member = \IPS\Member::load( $members_to_add->current() );

			if( !is_null( $member ) && !$member->isAdmin() && !in_array( $group_conseil_alliance, $member->get_groups() ) && !in_array( $group_alliance, $member->get_groups() ) )
			{
				$member->__set( 'member_group_id', $group_alliance );
				$member->__set( 'mgroup_others', '' );
				$member->save();
			}

			$members[]=$members_to_add->current();
		}

		$members_to_delete = \IPS\Db::i()->select( 'member_id', 'core_members', array( array( 'member_group_id=? OR find_in_set(?,mgroup_others) <> 0 or member_group_id=? OR find_in_set(?,mgroup_others) <> 0', $group_alliance, $group_alliance, $group_conseil_alliance, $group_conseil_alliance ) ) );

		for( $i = 0; $i < $members_to_delete->count(); $i++ )
		{
			$members_to_delete->next();
			$member = \IPS\Member::load( $members_to_delete->current() );

			if( !is_null( $member ) && !$member->isAdmin() )
			{
				$delete = true;

				foreach( $clubs as $club )
				{
					if( in_array( $club, $member->clubs() ) )
					{
						$delete = false;
						break;
					}
				}

				foreach( $club_not_delete as $club )
				{
					if( in_array( $club, $member->clubs() ) )
					{
						$delete = false;
						break;
					}
				}

				if( $delete )
				{
					$member->__set( 'member_group_id', $group_non_membre );
					$member->__set( 'mgroup_others', '' );
					$member->save();
				}
			}
		}

		return NULL;
	}
	
	/**
	 * Cleanup
	 *
	 * If your task takes longer than 15 minutes to run, this method
	 * will be called before execute(). Use it to clean up anything which
	 * may not have been done
	 *
	 * @return	void
	 */
	public function cleanup()
	{
		
	}
}
