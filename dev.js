import fs from 'fs';
import https from 'https';
import os from 'os';
import path from 'path';
import unzipper from 'unzipper';
import { execFileSync, spawnSync } from 'node:child_process';
import downloader from 'nodejs-file-downloader';

const mediaWikiVersion = '1.42.5';
const rootPath = path.dirname(process.argv[1]);
const mediawikiPath = rootPath + path.sep + 'mediawiki';
const pathToRun = mediawikiPath + path.sep + 'maintenance' + path.sep + 'run';

await (async () => {
  if (!fs.existsSync(mediawikiPath)) {
    fs.mkdirSync(mediawikiPath);
  }

  // Download FrankenPHP
  // Originally `curl https://frankenphp.dev/install.sh | sh`
  if (!fs.existsSync(mediawikiPath + path.sep + 'frankenphp')) {
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
      case 'Windows_NT':
        console.log(
          '❗ Use WSL to run FrankenPHP: https://learn.microsoft.com/windows/wsl/'
          // Or recommand dockerized FrankenPHP or femiwiki/docker-mediawiki
        );
        process.exit(1);
    }
    if (theArchBin == null) {
      console.error(`❗ FrankenPHP is not supported on ${osType} and ${arch}`);
      process.exit(1);
    }

    console.log(`📦 Downloading FrankenPHP for ${osType} (${arch})`);
    const url = `https://github.com/dunglas/frankenphp/releases/download/v1.5.0/${theArchBin}`;

    await new downloader({
      url: url,
      directory: mediawikiPath,
      fileName: 'frankenphp',
    }).download();
    console.log(`🥳 FrankenPHP downloaded successfully to ${mediawikiPath}`);
  } else {
    console.log(`✔ FrankenPHP downloaded already`);
  }
  fs.chmodSync(mediawikiPath + path.sep + 'frankenphp', 0o555);

  // Download MediaWiki.zip
  if (!fs.existsSync(mediawikiPath + path.sep + 'index.php')) {
    const pathToZip = rootPath + path.sep + 'mediawiki.zip';
    if (!fs.existsSync(pathToZip)) {
      const shortVer = mediaWikiVersion.split('.').slice(0, 2).join('.');
      const url = `https://releases.wikimedia.org/mediawiki/${shortVer}/mediawiki-${mediaWikiVersion}.zip`;
      console.log(`📦 Downloading mediawiki.zip`);
      await downloadFileToPath(url, pathToZip);
      console.log(`🥳 MediaWiki downloaded successfully to ${pathToZip}`);
    }
    const directory = await unzipper.Open.file(pathToZip);
    await directory.extract({ path: rootPath });
    if (fs.existsSync(mediawikiPath)) {
      await fs.renameSync(
        mediawikiPath + path.sep + 'frankenphp',
        rootPath + path.sep + 'mediawiki-1.42.5' + path.sep + 'frankenphp'
      );
    }
    await fs.renameSync('mediawiki-1.42.5', mediawikiPath);
    console.log(`🥳 MediaWiki unzipped successfully to ${mediawikiPath}`);
  } else {
    console.log(`✔ MediaWiki downloaded already`);
  }

  // Make a symbolic link to skin
  const pathToSkinInMw =
    mediawikiPath + path.sep + 'skins' + path.sep + 'Femiwiki';
  if (!fs.existsSync(pathToSkinInMw)) {
    await fs.symlinkSync(rootPath, pathToSkinInMw, 'dir');
  } else {
    console.log(`✔ A symbolic link from MediaWiki to the skin exists`);
  }

  // Initialize DB
  if (!fs.existsSync(mediawikiPath + path.sep + 'LocalSettings.php')) {
    console.log('📦 Installing MediaWiki');
    let buffer = await spawnSync(mediawikiPath + path.sep + 'frankenphp', [
      'php-cli',
      pathToRun,
      'install',
      '--server=http://127.0.0.1:2019',
      '--scriptpath=',
      '--dbtype=sqlite',
      '--dbname=femiwiki',
      `--dbpath=${mediawikiPath}`,
      '--pass=Adminpassword',
      '--with-extensions',
      'femiwiki',
      'Admin',
    ]).stdout;
    console.log(buffer.toString());

    console.log('🛢️ Initializing database');
    buffer = await spawnSync(mediawikiPath + path.sep + 'frankenphp', [
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
        mediawikiPath + path.sep + 'LocalSettings.php',
        appendant + '\n'
      );
    }

    await spawnSync(
      mediawikiPath + path.sep + 'frankenphp',
      ['php-cli', pathToRun, 'edit', 'Main Page'],
      {
        input:
          '* ID: Admin\n* PW: Adminpassword\n\nGo to [[Special:UserLogin]]',
      }
    );
  } else {
    console.log(`✔ The MediaWiki installation initialized already`);
  }

  console.log(
    '\n🥳 You can now visit <http://127.0.0.1:2019> to view your wiki.'
  );
  console.log('  ID: Admin');
  console.log('  Password: Adminpassword');
})();
