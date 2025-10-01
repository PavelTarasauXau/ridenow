let str = prompt('Input text: ');
str = str.toLowerCase();
const toSet = new Set(str);

document.write(`<h1>${toSet.size}</h1>`);
