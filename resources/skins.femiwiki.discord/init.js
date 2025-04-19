(function () {
  'use strict';

  async function init() {
    await mw.loader.getScript(
      'https://cdn.jsdelivr.net/npm/@widgetbot/crate@3',
    );
    new Crate({
      server: '314953743185477644',
      channel: '314953743185477644',
    });
  }

  init();
})();
