const fs = require('fs');
const path = require('path');
const tailwindConfig = require('./tailwind.config.js');

let themeJSON = {
    "$schema": "https://schemas.wp.org/trunk/theme.json",
    "version": 2,
    "settings": {
        "appearanceTools": true,
        "color": {
            "duotone": [],
            "palette": []
        },
        "spacing": {
            "units": ["%", "px", "em", "rem", "vh", "vw"],
            "spacingSizes": [],
        },
        "typography": {
            "fontSizes": []
        }
    }
};

// Convert Tailwind spacings to theme.json format
for (let spacingName in tailwindConfig.theme.spacing) {
    let spacingValue = tailwindConfig.theme.spacing[spacingName];
    themeJSON.settings.spacing.spacingSizes.push({
        "size": spacingValue,
        "slug": spacingName,
        "name": capitalize(spacingName)
    });
}

// Convert Tailwind colors to theme.json format
for (let colorName in tailwindConfig.theme.extend.colors) {
    let colorValue = tailwindConfig.theme.extend.colors[colorName];

    themeJSON.settings.color.palette.push({
        "color": colorValue,
        "name": convertToCamelCase(colorName),
        "slug": colorName,
    });
}

// Convert Tailwind font-sizes to theme.json format
for (let fontSizeName in tailwindConfig.theme.fontSize) {
    let fontSizeValue = tailwindConfig.theme.fontSize[fontSizeName];
    themeJSON.settings.typography.fontSizes.push({
        "size": fontSizeValue[0],
        "slug": fontSizeName,
    });
}


// Function to capitalize variable
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Function to convert variable camelCase
function convertToCamelCase(str) {
    return str.split('-').map((word, index) =>
        index !== 0 ? word.charAt(0).toUpperCase() + word.slice(1) : word
    ).join('');
}

// Write theme.json file
fs.writeFileSync(path.join(__dirname, 'theme.json'), JSON.stringify(themeJSON, null, 2));

console.log('theme.json generated from tailwind.config.js');
