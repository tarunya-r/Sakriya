<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ivole_Email_Footer' ) ) :

	require_once('class-ivole-email.php');

	class Ivole_Email_Footer {
	  public function __construct() {
	  }

		public static function get_text() {
      $language = get_option( 'ivole_language', 'EN' );
      $footer = '';
      switch ($language) {
        case 'EN':
          $footer = 'This email was sent by Customer Reviews Plugin on behalf of {{{shop.name}}}.<br>If you do not want to receive any more emails from Customer Reviews, please <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">unsubscribe</a>.<br>Postal address of Customer Reviews is 71–75 Shelton Street, London, WC2H 9JQ, United Kingdom.';
          break;
        case 'SV':
          $footer = 'Detta meddelande skickades av Customer Reviews Plugin på uppdrag av {{{shop.name}}}.<br>Om du inte vill få fler e-postmeddelanden från Customer Reviews så kan du <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">avprenumerera</a>.<br>Adressen till Customer Reviews är 71–75 Shelton Street, London, WC2H 9JQ, United Kingdom.';
          break;
        case 'FR':
          $footer = 'Cet e-mail vous a été envoyé par Customer Reviews, site d\'avis clients, pour le compte de {{{shop.name}}}.<br>Si vous ne souhaitez plus recevoir d\'e-mails de la part de Customer Reviews, merci de vous <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">désabonner</a>.<br>L\'adresse postale de Customer Reviews: 71–75 Shelton Street, Londres, WC2H 9JQ, Royaume-Uni.';
					break;
        case 'ES':
          $footer = 'Este correo electrónico ha sido enviado por el Customer Reviews Plugin en nombre de {{{shop.name}}}.<br>Si no desea recibir más correos electrónicos, por favor <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">anula la suscripción</a>.<br>La dirección de Customer Reviews es 71–75 Shelton Street, Londres, WC2H 9JQ, Reino Unido.';
          break;
        case 'DE':
          $footer = 'Diese email wurde vom Customer Reviews Plugin im auftrag von {{{shop.name}}} verschickt.<br>Wenn sie keine weiteren emails von Customer Reviews erhalten wollen, bitte <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">melden sie sich hier ab</a>.<br>Die adresse von Customer Reviews lautet 71–75 Shelton Street, London, WC2H 9JQ, Großbritannien.';
          break;
        case 'CS':
          $footer = 'Tento email jsme vám zaslali pomocí pluginu pro Customer Reviews Plugin, jménem {{{shop.name}}}.<br>Pokud nechcete dostávat tyto emaily, prosím <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">odhlašte se</a>.<br>Poštovní adresa pluginu Customer Reviews: 71–75 Shelton Street, Londýn, WC2H 9JQ, Spojené Království.';
          break;
        case 'PT':
          $footer = 'Este email foi enviado pelo Plugin Customer Reviews em nome de {{{shop.name}}}.<br>Se não pretende receber mais emails de Customer Reviews, por favor <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">cancele a subscrição</a>.<br>A morada de Customer Reviews é 71–75 Shelton Street, Londres, WC2H 9JQ, Reino Unido.';
          break;
				case 'NL':
          $footer = 'Deze email werd verzonden door Customer Reviews Plugin namens {{{shop.name}}}.<br>Wil je deze emails niet meer ontvangen, kun je hier <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">uitschrijven</a>.<br>Het postadres van Customer Reviews is 71–75 Shelton Street, Londen, WC2H 9JQ, Verenigd Koningkrijk.';
          break;
				case 'HU':
          $footer = 'Ez az e-mail a(z) {{{shop.name}}} rendszeréből érkezett a Customer Reviews használatával.<br>Ha nem szeretnél több ilyen e-mailt kapni, bármikor <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">leiratkozhatsz</a>.<br>A Customer Reviews postai címe: 71–75 Shelton Street, London, WC2H 9JQ, Egyesült Királyság.';
          break;
				case 'FI':
          $footer = 'Tämä sähköpostin lähetti Customer Reviews Plugin {{{shop.name}}} puolesta.<br>Jos et halua lisää sähköposteja Customer Reviews iltä, ole ystävällinen ja <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">peruuta tilaus</a>.<br>Postiosoite Customer Reviews on 71–75 Shelton Street, Lontoo, WC2H 9JQ, Yhdistyneet Kuningaskunnat.';
          break;
				case 'SL':
          $footer = 'To sporočilo ste prejeli od Customer Reviews v imenu {{{shop.name}}}.<br>Če ne želite več prejemati sporočil od Customer Reviews, <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">se odjavite</a>.<br>Naslov Customer Reviews je 71–75 Shelton Street, London, WC2H 9JQ, Združeno Kraljestvo.';
          break;
				case 'SR':
          $footer = 'Ovaj email je poslat od strane dodatka Customer Reviews koji stoji iza {{{shop.name}}}.<br>Ukoliko više ne želite da primate email-ove od Customer Reviews, molimo <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">prekinite pretplatu</a>.<br>Poštanska adresa Customer Reviews je 71–75 Shelton Street, London, WC2H 9JQ, Ujedinjeno Kraljevstvo.';
          break;
				case 'DA':
          $footer = 'Denne e-mail er sendt af Customer Reviews på vegne af {{{shop.name}}}.<br>Hvis du ikke ønsker at modtage tilsvarende mails fremover, bedes du <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">afmelde</a>.<br>Post adresse til Customer Reviews er 71–75 Shelton Street, London, WC2H 9JQ, Det Forenede Kongerige.';
          break;
				case 'RO':
	        $footer = 'Acest email a fost trimis de Customer Reviews Plugin pentru {{{shop.name}}}.<br>Daca nu mai vrei sa primesti email-uri de la Customer Reviews, te poti <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">dezabona</a>.<br>Adresa postala a Customer Reviews este 71–75 Shelton Street, Londra, WC2H 9JQ, Marea Britanie.';
	        break;
				case 'IT':
	        $footer = 'Questa email è stata inviata tramite Customer Reviews Plugin installato su {{{shop.name}}}.<br>Se non vuoi più ricevere altre emails da Customer Reviews, ti preghiamo di <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">cancellarti</a>.<br>Invia una lettera a: 71–75 Shelton Street, Londra, WC2H 9JQ, Regno Unito.';
	        break;
				case 'ID':
	        $footer = 'Email ini dikirim otomatis oleh plugin Customer Reviews, sebagai bagian dari {{{shop.name}}}.<br>Jika Anda tidak ingin menerima email seperti ini dari Customer Reviews, silahkan <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">berhenti berlangganan</a>.<br>Customer Reviews beralamatkan di 71–75 Shelton Street, London, WC2H 9JQ, United Kingdom.';
	        break;
				case 'RU':
	        $footer = 'Это письмо было отправлено плагином Customer Reviews от имени {{{shop.name}}}.<br>Если вы не хотите больше получать письма от Customer Reviews, пожалуйста <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">отпишитесь</a>.<br>Почтовый адрес Customer Reviews: 71–75 Shelton Street, Лондон, WC2H 9JQ, Великобритания.';
	        break;
        default:
          $footer = 'This email was sent by Customer Reviews Plugin on behalf of {{{shop.name}}}.<br>If you do not want to receive any more emails from Customer Reviews, please <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">unsubscribe</a>.<br>Postal address of Customer Reviews is 71–75 Shelton Street, London, WC2H 9JQ, United Kingdom.';
          break;
      }
      return $footer;
		}

		public static function get_from_name() {
      $language = get_option( 'ivole_language', 'EN' );
      $from_name = '';
      switch ($language) {
        case 'EN':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
        case 'SV':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
        case 'FR':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
					break;
        case 'ES':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
        case 'DE':
          $from_name = Ivole_Email::get_blogname() . ' mit CR';
          break;
        case 'CS':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
        case 'PT':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
				case 'NL':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
				case 'HU':
          $from_name = Ivole_Email::get_blogname() . ' a CR használatával';
          break;
				case 'FI':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
				case 'SL':
          $from_name = Ivole_Email::get_blogname() . ' preko CR';
          break;
				case 'SR':
          $from_name = Ivole_Email::get_blogname() . ' preko CR';
          break;
				case 'DA':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
				case 'RO':
          $from_name = Ivole_Email::get_blogname() . ' prin intermediul CR';
          break;
				case 'IT':
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
				case 'ID':
          $from_name = Ivole_Email::get_blogname() . ' lewat CR';
          break;
				case 'RU':
          $from_name = Ivole_Email::get_blogname() . ' через CR';
          break;
        default:
          $from_name = Ivole_Email::get_blogname() . ' via CR';
          break;
      }
      return $from_name;
		}
  }

endif;

?>
