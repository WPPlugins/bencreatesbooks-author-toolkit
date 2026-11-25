/**
 * AuthorPress admin scripts: media uploaders, color picker, wizard.
 */
(function( $ ) {
	'use strict';

	/* Media uploader fields (dashboard). */
	$( document ).on( 'click', '.bcbat-media-upload', function( e ) {
		e.preventDefault();
		var $field = $( this ).closest( '.bcbat-media-field' );
		var frame = wp.media({
			title: 'Choose image',
			button: { text: 'Use this image' },
			multiple: false
		});
		frame.on( 'select', function() {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$field.find( '.bcbat-media-id' ).val( attachment.id );
			$field.find( '.bcbat-media-preview' ).html(
				'<img src="' + attachment.sizes.medium.url + '" alt="">'
			);
		});
		frame.open();
	});

	$( document ).on( 'click', '.bcbat-media-remove', function( e ) {
		e.preventDefault();
		var $field = $( this ).closest( '.bcbat-media-field' );
		$field.find( '.bcbat-media-id' ).val( '' );
		$field.find( '.bcbat-media-preview' ).html( '' );
	});

	/* Palette selection highlight. */
	$( document ).on( 'change', '.bcbat-palette input', function() {
		$( '.bcbat-palette' ).removeClass( 'is-selected' );
		$( this ).closest( '.bcbat-palette' ).addClass( 'is-selected' );
		$( '#ap-accent' ).val( '' );
	});

	/* Color picker. */
	if ( $.fn.wpColorPicker ) {
		$( '.bcbat-color-field' ).wpColorPicker();
	}

	/* Wizard step navigation. */
	var currentStep = 0;
	function showStep( n ) {
		currentStep = n;
		$( '.bcbat-wizard-step' ).removeClass( 'is-active' );
		$( '.bcbat-wizard-step[data-step="' + n + '"]' ).addClass( 'is-active' );
		$( '.bcbat-wizard-progress span' ).each(function( i ) {
			$( this ).toggleClass( 'is-done', i <= n );
		});
		window.scrollTo( 0, 0 );
	}

	$( document ).on( 'click', '[data-goto]', function() {
		showStep( parseInt( $( this ).data( 'goto' ), 10 ) );
	});

	/* Add another book row (clones the template). */
	$( document ).on( 'click', '#bcbat-add-book', function( e ) {
		e.preventDefault();
		var $tpl = $( '#bcbat-book-template' );
		var index = $( '.bcbat-book-row' ).length;
		var $row = $tpl.clone().removeAttr( 'id' ).show();
		$row.find( '[name]' ).each(function() {
			var name = $( this ).attr( 'name' ).replace( '__i__', index );
			$( this ).attr( 'name', name );
		});
		$row.find( 'h4 .book-num' ).text( index + 1 );
		$( '#bcbat-books' ).append( $row );
	});

	$( document ).on( 'click', '.bcbat-remove-book', function( e ) {
		e.preventDefault();
		if ( $( '.bcbat-book-row' ).length > 1 ) {
			$( this ).closest( '.bcbat-book-row' ).remove();
		}
	});

	/* Wizard AJAX submit helper. */
	function wizardPost( action, data, $btn, onDone ) {
		var $spinner = $( '<span class="spinner is-active bcbat-spinner"></span>' );
		$btn.prop( 'disabled', true ).after( $spinner );
		data.action = action;
		data.nonce = bcbatWizard.nonce;
		$.post( bcbatWizard.ajaxurl, data )
			.done(function( response ) {
				if ( response.success ) {
					onDone( response.data );
				} else {
					alert( response.data && response.data.message ? response.data.message : 'Something went wrong.' );
				}
			})
			.fail(function() {
				alert( 'Request failed. Please try again.' );
			})
			.always(function() {
				$spinner.remove();
				$btn.prop( 'disabled', false );
			});
	}

	/* Demo import. */
	$( document ).on( 'click', '#bcbat-import-demo', function( e ) {
		e.preventDefault();
		var $confirm = $( '#bcbat-wipe-confirm' );
		if ( ! $confirm.is( ':checked' ) ) {
			$( '.bcbat-wipe-warning' ).addClass( 'is-attention' );
			$confirm.focus();
			return;
		}
		wizardPost( 'bcbat_import_demo', { confirmed: 1 }, $( this ), function() {
			showStep( 5 );
		});
	});
	$( document ).on( 'change', '#bcbat-wipe-confirm', function() {
		$( '.bcbat-wipe-warning' ).toggleClass( 'is-attention', ! $( this ).is( ':checked' ) );
	});

	/* Guided setup: collect and submit. */
	$( document ).on( 'click', '#bcbat-finish-setup', function( e ) {
		e.preventDefault();
		var books = [];
		$( '.bcbat-book-row' ).each(function() {
			var $row = $( this );
			var title = $row.find( '[name$="[title]"]' ).val();
			if ( ! title ) {
				return;
			}
			books.push({
				title: title,
				subtitle: $row.find( '[name$="[subtitle]"]' ).val(),
				genre: $row.find( '[name$="[genre]"]' ).val(),
				price: $row.find( '[name$="[price]"]' ).val(),
				coming_soon: $row.find( '[name$="[coming_soon]"]' ).is( ':checked' ) ? 1 : 0,
				buy_link: $row.find( '[name$="[buy_link]"]' ).val(),
				cover_id: $row.find( '.bcbat-media-id' ).val(),
				blurb: $row.find( '[name$="[blurb]"]' ).val()
			});
		});
		wizardPost( 'bcbat_guided_setup', {
			blogname: $( '#wz-blogname' ).val(),
			blogdescription: $( '#wz-blogdescription' ).val(),
			about_name: $( '#wz-about-name' ).val(),
			about_bio: $( '#wz-about-bio' ).val(),
			about_photo: $( '#wz-about-photo' ).val(),
			books: books
		}, $( this ), function() {
			showStep( 5 );
		});
	});

	/* ============ Command center (v1.1) ============ */

	/* Media uploader fields (command center). */
	$( document ).on( 'click', '.apdash-media-upload', function( e ) {
		e.preventDefault();
		var $field = $( this ).closest( '.apdash-media' );
		var frame = wp.media({
			title: 'Choose image',
			button: { text: 'Use this image' },
			multiple: false
		});
		frame.on( 'select', function() {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
			$field.find( '.apdash-media-id' ).val( attachment.id );
			$field.find( '.apdash-media-preview' ).html( '<img src="' + url + '" alt="">' );
		});
		frame.open();
	});

	$( document ).on( 'click', '.apdash-media-remove', function( e ) {
		e.preventDefault();
		var $field = $( this ).closest( '.apdash-media' );
		$field.find( '.apdash-media-id' ).val( '' );
		$field.find( '.apdash-media-preview' ).html( '' );
	});

	/* Repeaters: add / remove rows. */
	var apdashCounters = {};
	$( document ).on( 'click', '.apdash-rep-add', function( e ) {
		e.preventDefault();
		var tplName = $( this ).data( 'template' );
		var $tpl = $( '#apdash-tpl-' + tplName );
		if ( ! $tpl.length ) { return; }
		var $repeater = $( this ).siblings( '.apdash-repeater' );
		if ( ! apdashCounters[ tplName ] ) {
			apdashCounters[ tplName ] = $repeater.find( '.apdash-rep-row' ).length;
		}
		var idx = apdashCounters[ tplName ]++;
		var html = $tpl.html().replace( /__i__/g, idx );
		$repeater.append( html );
	});
	$( document ).on( 'click', '.apdash-rep-remove', function( e ) {
		e.preventDefault();
		$( this ).closest( '.apdash-rep-row' ).remove();
	});

	/* Palette selection highlight (command center). */
	$( document ).on( 'change', '.apdash-palette input', function() {
		$( '.apdash-palette' ).removeClass( 'is-selected' );
		$( this ).closest( '.apdash-palette' ).addClass( 'is-selected' );
		$( '.apdash-color' ).val( '' );
	});
	if ( $.fn.wpColorPicker ) {
		$( '.apdash-color' ).wpColorPicker();
	}

	/* Newsletter: suggest the email field name per provider. */
	$( document ).on( 'change', '#apdash-nl-provider', function() {
		var map = ( typeof apNlFields !== 'undefined' ) ? apNlFields : {};
		var field = map[ $( this ).val() ] || 'EMAIL';
		$( 'input[name="nl_field"]' ).val( field );
	});

})( jQuery );
