const fs = require('fs');
const path = require('path');

const changelogPath = path.resolve(__dirname, '../CHANGELOG.md');
const packageJson = require(path.resolve(__dirname, '../package.json'));
const repoUrl = packageJson.repository.url.replace(/\.git$/, '');

function updateChangelog() {
    let changelog = fs.readFileSync(changelogPath, 'utf8');

    // Entfernen aller bestehenden Linkblöcke
    changelog = changelog.replace(/\[(Unreleased|\d+.\d+.\d+)\]:\shttps.*/gm, '');


    // Anpassung des regulären Ausdrucks für Versionseinträge (mit und ohne Links)
    const versionMatches = changelog.match(/## \[\d+\.\d+\.\d+\]\(https:\/\/github\.com\/[^/]+\/[^/]+\/compare\/v\d+\.\d+\.\d+\.\.\.v\d+\.\d+\.\d+\) \(\d{4}-\d{2}-\d{2}\)|## \d+\.\d+\.\d+ - \d{4}-\d{2}-\d{2}/g);

    if (versionMatches) {
        // Ersetze die Versionseinträge im Changelog (alle zu einheitlichem Format)
        versionMatches.forEach(match => {
            if (match.includes('](')) {
                const version = match.match(/(\d+\.\d+\.\d+)/)[0];
                const date = match.match(/\((\d{4}-\d{2}-\d{2})\)/)[1];
                const newFormat = `${version} - ${date}`;
                changelog = changelog.replace(match, `## ${newFormat}`);
            }
        });


        // Ändere Aufzählungspunkte von '*' zu '-'
        changelog = changelog.replace(/^\* /gm, '- ');

        const versionLinks = versionMatches.map((match, index) => {
            const version = match.match(/\d+\.\d+\.\d+/)[0];
            const previousVersion = index < versionMatches.length - 1 ? versionMatches[index + 1].match(/\d+\.\d+\.\d+/)[0] : null;
            if (previousVersion) {
                return `[${version}]: ${repoUrl}/compare/v${previousVersion}...v${version}`;
            }
            return `[${version}]: ${repoUrl}/tag/v${version}`;
        });


        // Entfernen von Leerzeilen
        changelog = changelog.replace(/(^## \d+\.\d+\.\d+ - \d{4}-\d{2}-\d{2})\n*/gm, '$1\n\n');
        changelog = changelog.replace(/(^### .*)\n*/gm, '$1\n\n')
        changelog = changelog.replace(/(^- .*)\n*/gm, '$1\n')
        changelog = changelog.replace(/^\n*(## )/gm, '\n\n$1')


        const unreleasedLink = `[Unreleased]: ${repoUrl}/compare/v${versionMatches[0].match(/\d+\.\d+\.\d+/)[0]}...main`;

        // Füge neuen Link-Block hinzu
        const newLinkBlock = `\n\n${unreleasedLink}\n${versionLinks.join('\n')}`;
        changelog += newLinkBlock;

       changelog = changelog.replace(/^(# .*)\n*/gm,'$1\n\n') ;
       changelog = changelog.replace(/(-\s.*)\n*(\[Unreleased\])/gm,'$1\n\n\n$2') ;
       console.log(changelog);
        fs.writeFileSync(changelogPath, changelog, 'utf8');
    }
}

updateChangelog();
