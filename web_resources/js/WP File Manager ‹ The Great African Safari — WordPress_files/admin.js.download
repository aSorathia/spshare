(function($) {

  // ======================================================
  if ( !$.CREATE ) {
    $.CREATE = {};
  }
  // ======================================================


  // ======================================================
  // CREATE MEGA MENU
  // ======================================================
  $.CREATE.megamenu = function( el ){
    var base  = this;

    // Access to jQuery and DOM versions of element
    base.$el  = $(el);
    base.el   = el;

    // Add a reverse reference to the DOM object
    base.$el.data( "CREATE.megamenu" , base );

    base.init = function () {

      var _timeout  = 0,
          _menu     = base.$el;

      _menu.on('click', '.is-mega', function(){
        base.flush( $(this) );
        base.depends( _menu );
      });

      _menu.on( 'mouseup', '.menu-item-bar', function(){
        clearTimeout( _timeout );
        _timeout = setTimeout( function(){ base.depends(); }, 50 );
      });

      _menu.on('change', '.is-width', function(){
        var _this       = $(this),
            _container  = _this.closest('.mega-menu');

        if( _this.val() == 'custom' || _this.val() == 'natural' ) {
          _container.find('.mega-depend-position').removeClass('hidden');
        } else {
          _container.find('.mega-depend-position').addClass('hidden');
        }

        if( _this.val() == 'custom' ) {
          _container.find('.mega-depend-width').removeClass('hidden');
        } else {
          _container.find('.mega-depend-width').addClass('hidden');
        }
      });

      $('.is-width').trigger('change');

      base.depends();
    };

    base.depends = function(){

      var _menu = base.$el;

      _menu.find('.is-mega').each(function (){
        base.flush( $(this) );
      });

      // clear all mega columns
      $('li', _menu).removeClass('active-mega-column').removeClass('active-sub-mega-column');

      // add columns for mega menu
      var nextDepth = $('.active-mega-menu', _menu).nextUntil('.menu-item-depth-0', 'li');
      nextDepth.closest('li.menu-item-depth-1').addClass('active-mega-column');
      nextDepth.closest('li:not(.menu-item-depth-1)').addClass('active-sub-mega-column');
    };

    base.flush = function( _el ){
      if( _el.is(':checked') ){
        _el.closest('li').addClass('active-mega-menu');
        _el.closest('li').find('.field-mega-width').removeClass('hidden');
      }else{
        _el.closest('li').find('.field-mega-width').addClass('hidden');
        _el.closest('li').removeClass('active-mega-menu');
      }
    };

    // Run initializer
    base.init();
  };
  $.fn.CREATE_megamenu = function () {
    return this.each(function () {
      new $.CREATE.megamenu( this );
    });
  };

  $(document).ready( function(){
	$('#menu-to-edit').CREATE_megamenu();
  });

}(jQuery));