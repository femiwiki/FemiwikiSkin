import fs from 'fs';
import os from 'os';
import path from 'path';
import unzipper from 'unzipper';
import { spawnSync } from 'node:child_process';
import downloader from 'nodejs-file-downloader';

const mediaWikiVersion = '1.43.1';
const frankenPhpVersion = '1.4.4';
const caddyPort = '2019';
const rootPath = path.dirname(process.argv[1]);
const pathToMediaWiki = rootPath + path.sep + 'mediawiki';
const pathToRun = pathToMediaWiki + path.sep + 'maintenance' + path.sep + 'run';
const frakenphpFilename = 'frankenphp';
const pathToFrankenPhp = pathToMediaWiki + path.sep + frakenphpFilename;

const mwUsername = 'Admin';
const mwPassword = 'Adminpassword';

await (async () => {
  if (!fs.existsSync(pathToMediaWiki)) {
    fs.mkdirSync(pathToMediaWiki);
  }

  // Download FrankenPHP
  // Originally `curl https://frankenphp.dev/install.sh | sh`
  if (!fs.existsSync(pathToFrankenPhp)) {
    let theArchBin = null;
    const osType = os.type();
    const arch = os.arch();
    switch (osType) {
      case 'Linux':
        switch (arch) {
          case 'arm64':
            theArchBin = 'frankenphp-linux-aarch64';
            break;
          case 'x64':
            theArchBin = 'frankenphp-linux-x86_64';
        }
        break;
      case 'Darwin':
        switch (arch) {
          case 'arm64':
            theArchBin = 'frankenphp-mac-arm64';
            break;
          default:
            theArchBin = 'frankenphp-mac-x86_64';
            break;
        }
        break;
      case 'Windows_NT':
        console.log(
          '❗ We use FrakenPHP for our development environment and it which does not support Windows.' +
            'See https://github.com/dunglas/frankenphp/issues/83 for details.\n' +
            '  There are some solutions:\n' +
            '  - You can use WSL to run this command: https://learn.microsoft.com/windows/wsl/\n' +
            '  - You can use Docker Desktop to run FrankenPHP: https://docs.docker.com/desktop/setup/install/windows-install/\n' +
            '  - Or you can try https://github.com/femiwiki/docker-mediawiki\n'
        );
        process.exit(1);
    }
    if (theArchBin == null) {
      console.error(`❗ FrankenPHP is not supported on ${osType} and ${arch}`);
      process.exit(1);
    }

    console.log(`📦 Downloading FrankenPHP for ${osType} (${arch})`);
    await new downloader({
      url: `https://github.com/dunglas/frankenphp/releases/download/v${frankenPhpVersion}/${theArchBin}`,
      directory: pathToMediaWiki,
      fileName: frakenphpFilename,
    }).download();
    console.log(`🥳 FrankenPHP downloaded successfully to ${pathToMediaWiki}`);
    fs.chmodSync(pathToFrankenPhp, 0o555);
  } else {
    console.log(`✔ FrankenPHP downloaded already`);
  }

  // Download MediaWiki.zip
  if (!fs.existsSync(pathToMediaWiki + path.sep + 'index.php')) {
    const zipFilename = 'mediawiki.zip';
    const pathToZip = rootPath + path.sep + zipFilename;
    if (!fs.existsSync(pathToZip)) {
      const shortVer = mediaWikiVersion.split('.').slice(0, 2).join('.');
      console.log(`📦 Downloading mediawiki.zip`);
      await new downloader({
        url: `https://releases.wikimedia.org/mediawiki/${shortVer}/mediawiki-${mediaWikiVersion}.zip`,
        directory: rootPath,
        fileName: zipFilename,
      }).download();
      console.log(`🥳 MediaWiki downloaded successfully to ${rootPath}`);
    }
    console.log(`📦 Extracting mediawiki.zip`);
    const directory = await unzipper.Open.file(pathToZip);
    await directory.extract({ path: rootPath });
    if (fs.existsSync(pathToMediaWiki)) {
      fs.renameSync(
        pathToFrankenPhp,
        rootPath +
          path.sep +
          `mediawiki-${mediaWikiVersion}` +
          path.sep +
          'frankenphp'
      );
    }
    fs.renameSync(`mediawiki-${mediaWikiVersion}`, pathToMediaWiki);
    console.log(`🥳 MediaWiki unzipped successfully to ${pathToMediaWiki}`);
  } else {
    console.log(`✔ MediaWiki downloaded already`);
  }

  // Make a symbolic link to skin
  const pathToSkinInMw =
    pathToMediaWiki + path.sep + 'skins' + path.sep + 'Femiwiki';
  if (!fs.existsSync(pathToSkinInMw)) {
    fs.symlinkSync(rootPath, pathToSkinInMw, 'dir');
  } else {
    console.log(`✔ A symbolic link from MediaWiki to the skin exists`);
  }

  // Initialize DB
  if (!fs.existsSync(pathToMediaWiki + path.sep + 'LocalSettings.php')) {
    console.log('📦 Installing MediaWiki');
    let buffer = spawnSync(pathToMediaWiki + path.sep + 'frankenphp', [
      'php-cli',
      pathToRun,
      'install',
      `--server=http://localhost:${caddyPort}`,
      '--scriptpath=',
      '--dbtype=sqlite',
      '--dbname=femiwiki',
      `--dbpath=${pathToMediaWiki}`,
      `--pass=${mwPassword}`,
      '--with-extensions',
      'femiwiki',
      mwUsername,
    ]).stdout;
    console.log(buffer.toString());

    console.log('🛢️ Initializing database');
    buffer = spawnSync(pathToFrankenPhp, [
      'php-cli',
      pathToRun,
      'update',
      '--quick',
    ]).stdout;
    console.log(buffer.toString());

    for (const appendant of [
      "$wgDefaultSkin = 'femiwiki';",
      // Hide PHP errors
      "ini_set('display_errors', 0);",
      // Disable browser cache
      "$wgResourceLoaderMaxage = ['unversioned' => 0];",
    ]) {
      fs.appendFileSync(
        pathToMediaWiki + path.sep + 'LocalSettings.php',
        appendant + '\n'
      );
    }

    spawnSync(pathToFrankenPhp, ['php-cli', pathToRun, 'edit', 'Main Page'], {
      input: '* ID: Admin\n* PW: Adminpassword\n\nGo to [[Special:UserLogin]]',
    });
  } else {
    console.log(`✔ The MediaWiki installation initialized already`);
  }

  console.log(
    `\n🥳 You can now visit <http://localhost:${caddyPort}> to view your wiki.`
  );
  console.log(`  ID: ${mwUsername}`);
  console.log(`  Password: ${mwPassword}`);
})();
