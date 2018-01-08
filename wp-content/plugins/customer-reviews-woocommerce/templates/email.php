<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//qTranslate integration
if( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
	echo qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( wpautop( wp_kses_post( get_option( 'ivole_email_body', $def_body ) ) ) );
} else {
	echo wpautop( wp_kses_post( get_option( 'ivole_email_body', $def_body ) ) );
}
