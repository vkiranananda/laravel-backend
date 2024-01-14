const icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><line x1="6" x2="10" y1="12" y2="12" stroke="currentColor" stroke-linecap="round" stroke-width="2"/><line x1="14" x2="18" y1="12" y2="12" stroke="currentColor" stroke-linecap="round" stroke-width="2"/></svg>';
const emptyIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">' + '</svg>';

class c {
    constructor({data}) {
        this.data = {type: (data.type ? data.type : 'space')}
    }

    render() {
        this.el = document.createElement("div")
        this.renderCurrentEl()
        return this.el
    }

    renderCurrentEl() {
        if (this.types[this.data.type]) {
            this.el.innerHTML = this.types[this.data.type].html
        } else {
            this.el.innerHTML = '<i>Тип данных не существует</i>'
            console.log(this.data.type)
        }
    }

    renderSettings() {
        let res = []
        for (let key in this.types) {
            res.push({
                icon: this.types[key].icon,
                label: this.types[key].name,
                onActivate: () => this.setType(key),
                closeOnActivate: !0,
                isActive: this.data.type === key
            })
        }
        return res
    }

    save(e) {
        return this.data;
    }

    setType(key) {
        this.data.type = key
        this.renderCurrentEl()
    }

    types = {
        'space': {
            name: "Пустая строка",
            html: "<div>&nbsp;</div>",
            icon: emptyIcon
        },
        '***': {
            name: "* * *",
            html: "<div class='text-center h2 pt-4 pb-0 m-0 '>* * *</div>",
            icon: emptyIcon
        }
    };

    static get toolbox() {
        return {
            icon: icon,
            title: "Разделитель"
        };
    }
}

export {
    c as default
};
