//<?php

$clubs = json_decode( \IPS\Settings::i()->promote_clubs );
$not_delete_members_in_club = json_decode( \IPS\Settings::i()->not_delete_members_in_club );
$group_alliance = \IPS\Settings::i()->group_alliance;
$group_conseil_alliance = \IPS\Settings::i()->group_conseil_alliance;
$group_non_membre = \IPS\Settings::i()->group_non_membre;

$all_clubs = \IPS\Db::i()->select( '*', 'core_clubs' );

$optionsClub = array();

foreach( $all_clubs as $k => $v )
{
	$optionsClub[ $v[ 'id' ] ] = $v[ 'name' ];
}

$form->add( new \IPS\Helpers\Form\Select( 'promote_clubs', $clubs, FALSE, array( 'options' => $optionsClub, 'sort' => TRUE, 'multiple' => true, 'parse' => 'normal' ) ) );
$form->add( new \IPS\Helpers\Form\Select( 'not_delete_members_in_club', $not_delete_members_in_club, FALSE, array( 'options' => $optionsClub, 'sort' => TRUE, 'multiple' => true, 'parse' => 'normal' ) ) );
$form->add( new \IPS\Helpers\Form\Select( 'group_alliance', $group_alliance, TRUE, array( 'options' => \IPS\Member\Group::groups(), 'sort' => TRUE, 'parse' => 'normal' ) ) );
$form->add( new \IPS\Helpers\Form\Select( 'group_conseil_alliance', $group_conseil_alliance, TRUE, array( 'options' => \IPS\Member\Group::groups(), 'sort' => TRUE, 'parse' => 'normal' ) ) );
$form->add( new \IPS\Helpers\Form\Select( 'group_non_membre', $group_non_membre, TRUE, array( 'options' => \IPS\Member\Group::groups(), 'sort' => TRUE, 'parse' => 'normal' ) ) );


if ( $values = $form->values() )
{
	$values['promote_clubs'] = json_encode( array_values( $values['promote_clubs'] ) );
	$values['not_delete_members_in_club'] = json_encode( array_values( $values['not_delete_members_in_club'] ) );

	$form->saveAsSettings( $values );
	return TRUE;
}

return $form;