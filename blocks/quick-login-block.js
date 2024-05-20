(function () {
  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;

  registerBlockType('better-quick-login/quick-login-block', {
    title: 'Quick Login Block',
    icon: 'shield',
    category: 'common',

    edit: function () {
      return el('div', { className: 'quick-login-block' }, '[bqlc_quicklogin]');
    },

    save: function () {
      return el('div', { className: 'quick-login-block' }, '[bqlc_quicklogin]');
    },
  });
})();