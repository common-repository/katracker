<?php

// External Downloads //////////////////////////////////////////////////////////////////////////////

if ( isset( $_GET['info_hash'] ) && katracker_valid_hash_info( $_GET['info_hash'] ) ) {
	$_GET['torrent'] = get_torrent_id_from_hash( $_GET['info_hash'] );
}
if ( !isset( $_GET['torrent'] ) ) return;
$torrent_id = intval( $_GET['torrent'] );
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'torrent/torrent-class.php';

if ( get_katracker_option( 'open-tracker' ) ||
     get_katracker_option( 'tracked-access' ) ||
     get_torrent_meta( $_GET['torrent'], 'track' ) ) {
	$torrent = new TorrentFile( get_attached_file( $torrent_id ) );
	$reset_comment  = get_katracker_option( 'reset-comment' );
	$reset_announce = get_katracker_option( 'reset-announce' );

	if ( $reset_announce == 2 ) {
		$torrent->announce( false );
		$torrent->announce( get_katracker_option( 'default-announce' ) );
	} else if ( $reset_announce == 1 ) {
		$torrent->announce( get_katracker_option( 'default-announce' ) );
	}

	if ( $reset_comment == 2 ) {
		$torrent->comment( get_katracker_option( 'default-comment' ) );
	} else if( $reset_comment == 1) {
		$torrent->comment( $torrent->comment() . PHP_EOL . get_katracker_option( 'default-comment' ) );
	}
	
	$filename = get_katracker_option( 'rename-files' ) ?
	            sanitize_file_name( get_the_title( $_GET['torrent'] ) ) :
	            preg_replace( '/,/', '', $torrent->name() );
	
	$hits = get_torrent_meta( $_GET['torrent'], 'hits');
	update_torrent_meta( $_GET['torrent'], 'hits', $hits + 1);
	
	$torrent->send( $filename . '.torrent' );
	exit;
}

?>
