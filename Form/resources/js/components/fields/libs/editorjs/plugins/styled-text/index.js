const iconBlock = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M4 18L10 4L16 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 14H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
const iconEmpty = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">' + '</svg>';

class StyledText {
    constructor({data, config, api}) {
        this.api = api;
        this.config = config;
        this.data = {
            text: data.text || '',
            className: data.className || this.config.styles[0].className,
        };
        this.wrapper = undefined;
    }

    static get toolbox() {
        return {
            title: 'Styled Text',
            icon: iconBlock,
        };
    }

    render() {
        this.wrapper = document.createElement('div')
        this.wrapper.contentEditable = true;
        this.wrapper.innerHTML = this.data.text;

        this.renderElement(this.wrapper, this.data.className)

        return this.wrapper;
    }

    // Задаем стиль элементу
    renderElement(wrapper, className) {
        wrapper.className = className
    }

    save(blockContent) {
        return {
            text: blockContent.innerHTML,
            className: this.data.className,
        };
    }


    setClass(className) {
        this.data.className = this.wrapper.className = className
    }

    renderSettings() {
        return this.config.styles.map((s) => ({
            icon: iconEmpty,
            label: `<span class='${s.className}'>${s.label}</span>`,
            onActivate: () => this.setClass(s.className),
            closeOnActivate: !0,
            isActive: this.data.className === s.className,
            // render: () => document.createElement("div")
        }));
    }

    static get conversionConfig() {
        return {
            export: 'text',
            import: 'text',
        };
    }

    validate(savedData) {
        return savedData.text.trim() !== '';
    }

    static get isReadOnlySupported() {
        return true;
    }
}

export {
    StyledText as default
};
