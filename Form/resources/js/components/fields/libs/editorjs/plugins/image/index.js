import emitter from './../../../../../../../../../resources/js/libs/mitt'

class BackendImage {
    el = false;

    constructor({data}) {
        this.image = data;
    }

    static get toolbox() {
        return {
            title: 'Картинка',
            icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
        };
    }

    render() {
        if (!this.image.url) this.attachFileModal()

        this.el = document.createElement('div')
        this.el.innerHTML = '<i>Изображение не загружено</i>'

        this.localRender()
        return this.el
    }

    attachFileModal = (files, link) => {
        emitter.emit('UploadFilesModalShow', {
            type: 'image',
            showLink: true,
            count: 1,
            return: this.attachFile
        })
    }

    attachFile = (files, link) => {

        if (files.length > 0) this.image = {url: files[0].orig, id: files[0].id, link}

        this.localRender()
    }

    localRender() {
        if (!this.image.url) return
        let img = '<img alt="" title="" src="' + this.image.url + '" />'
        this.el.innerHTML = img;
        // res += (link) ? '<a href="' + file.orig + '">' + img + '</a> ' : img
    }

    save(blockContent) {
        return this.image
    }
}

export default BackendImage
