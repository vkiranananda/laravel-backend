class BigTool {

    static get isInline() {
        return true;
    }

    constructor({api: t}) {
        this.api = t
        this.tag = "BIG"
        this.button = null;
        this.state = false;
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.textContent = 'A+';

        this.button.classList.add(this.api.styles.inlineToolButton)
        // this.button.innerHTML = this.toolboxIcon

        return this.button;
    }

    surround(t) {
        if (!t)
            return;
        let e = this.api.selection.findParentTag(this.tag);
        e ? this.unwrap(e) : this.wrap(t);
    }

    checkState(selection) {
        const t = this.api.selection.findParentTag(this.tag);
        this.button.classList.toggle(this.api.styles.inlineToolButtonActive, !!t);
    }


    wrap(t) {
        let e = document.createElement(this.tag);
        e.appendChild(t.extractContents()), t.insertNode(e), this.api.selection.expandToTag(e);
    }

    unwrap(t) {
        this.api.selection.expandToTag(t);
        let e = window.getSelection(), n = e.getRangeAt(0), i = n.extractContents();
        t.parentNode.removeChild(t), n.insertNode(i), e.removeAllRanges(), e.addRange(n);
    }

    // Проверка валидности
    static get sanitize() {
        return {
            big: {}
        };
    }

    get toolboxIcon() {
        return o;
    }
}

export default BigTool


