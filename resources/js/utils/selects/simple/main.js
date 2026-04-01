export function loadSimpleSelect(id, icon = null, withClear = true) {
    const simpleSelect = document.getElementById(id);

    if (simpleSelect && !simpleSelect.tomselect) {

        const plugins = [];

        if (withClear) {
            plugins.push('clear_button');
        }

        const instance = new TomSelect(simpleSelect, {
            valueField: 'id',
            labelField: 'text',
            searchField: ['text', 'id'],
            create: false,
            sortField: {
                field: 'id',
                direction: 'desc'
            },
            plugins: plugins,

            render: {
                option: (item, escape) => `
                    <div>
                        ${icon ? icon : ''}
                        ${escape(item.text)}
                    </div>
                `,
                item: (item, escape) => `
                    <div>
                        ${icon ? icon : ''}
                        ${escape(item.text)}
                    </div>
                `
            }
        });

        return instance;
    }

    return null;
}
