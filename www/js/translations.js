i18n = {
    lang: 'cs',
    translate: function (translate) {
        var arr = translate.split('.');
        var tr = i18n[this.lang];
        for (var i = 0; i < arr.length; i++) {
            if (!tr[arr[i]]) {
                break;
            }
            tr = tr[arr[i]];
        }
        if (typeof tr === 'string') {
            return tr;
        } else {
            console.log('Translation for ' + translate + ' not found.');
            return translate;
        }
    }
};
// cs
i18n.cs = {
    selectize: {
        add: 'Přidat'
    }
};
// en
i18n.en = {
    selectize: {
        add: 'Add'
    }
};