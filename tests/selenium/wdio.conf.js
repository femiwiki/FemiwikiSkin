'use strict';

import { config } from 'wdio-mediawiki/wdio-defaults.conf.js';

const _config = {
  ...config,
  // Override, or add to, the setting from wdio-mediawiki.
  // Learn more at https://webdriver.io/docs/configurationfile/
  //
  // Example:
  // logLevel: 'info',
  maxInstances: 4,
  specs: ['./specs/**/*.js'],
};
export { _config as config };
