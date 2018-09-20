// Usage: node rename.js path/to/directory 'SEARCH' 'replace'
// Credit: https://gist.github.com/hpneo/c4da1ca88e56e6164e36

var fs = require('fs'),
    path = require('path'),
    args = process.argv.slice(2),
    dir = args[0],
    match = RegExp(args[1], 'g'),
    replace = args[2],
    files;

files = fs.readdirSync(dir);

files.filter(function(file) {
    return file.match(match);
}).forEach(function(file) {
    var filePath = path.join(dir, file),
        newFilePath = path.join(dir, file.replace(match, replace));

    fs.renameSync(filePath, newFilePath);
});
