//<?php

$clubs = json_decode( \IPS\Settings::i()->promote_clubs );
$group_alliance = \IPS\Settings::i()->group_alliance;
$group_conseil_alliance = \IPS\Settings::i()->group_conseil_alliance;
$group_non_membre = \IPS\Settings::i()->group_non_membre;

//$form->add( new \IPS\Helpers\Form\Text( 'promote_clubs', '', TRUE, array( 'autocomplete' => array( 'source' => 'app=avacalendar&module=calendar&controller=manage&do=getZone', 'maxItems' => 1, 'prefix' => FALSE ), 'placeholder' => 'avacalendar_calendar_manage_zone' ) ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;