import {default as axios} from "axios";

export default class ImageBlock {

    static get toolbox() {
        return {
            title: 'Картинки',
            icon: '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
                '\t viewBox="0 0 58 58" style="enable-background:new 0 0 58 58;" xml:space="preserve">\n' +
                '<g>\n' +
                '\t<path d="M31,56h24V32H31V56z M33,34h20v20h-9V41.414l4.293,4.293l1.414-1.414L43,37.586l-6.707,6.707l1.414,1.414L42,41.414V54h-9\n' +
                '\t\tV34z"/>\n' +
                '\t<path d="M21.569,13.569C21.569,10.498,19.071,8,16,8s-5.569,2.498-5.569,5.569c0,3.07,2.498,5.568,5.569,5.568\n' +
                '\t\tS21.569,16.64,21.569,13.569z M12.431,13.569C12.431,11.602,14.032,10,16,10s3.569,1.602,3.569,3.569S17.968,17.138,16,17.138\n' +
                '\t\tS12.431,15.537,12.431,13.569z"/>\n' +
                '\t<path d="M6.25,36.661C6.447,36.886,6.723,37,7,37c0.234,0,0.47-0.082,0.66-0.249l16.313-14.362l7.319,7.318\n' +
                '\t\tc0.391,0.391,1.023,0.391,1.414,0s0.391-1.023,0-1.414l-1.825-1.824l9.181-10.054l11.261,10.323\n' +
                '\t\tc0.408,0.373,1.04,0.345,1.413-0.062c0.373-0.407,0.346-1.04-0.062-1.413l-12-11c-0.196-0.179-0.452-0.279-0.72-0.262\n' +
                '\t\tc-0.265,0.012-0.515,0.129-0.694,0.325l-9.794,10.727l-4.743-4.743c-0.374-0.372-0.972-0.391-1.368-0.044L6.339,35.249\n' +
                '\t\tC5.925,35.614,5.884,36.246,6.25,36.661z"/>\n' +
                '\t<path d="M57,2H1C0.448,2,0,2.447,0,3v44c0,0.553,0.448,1,1,1h24c0.552,0,1-0.447,1-1s-0.448-1-1-1H2V4h54v23c0,0.553,0.448,1,1,1\n' +
                '\t\ts1-0.447,1-1V3C58,2.447,57.552,2,57,2z"/>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '<g>\n' +
                '</g>\n' +
                '</svg>'
        };
    }

    constructor({data, api, config}) {
        this.api = api;
        this.wrapper = undefined;
        this.data = data || []
        // this.buttons = Object.assign(this.transformButtons(),data)

        // for (const [key, element] of Object.entries(this.buttons)) {
        //     this.data[key] = {
        //         id: key,
        //         link: data[key]?.link || '',
        //         linkId : data[key]?.linkId || '',
        //         linkOriginal: data[key]?.linkOriginal || '',
        //         name: data[key]?.name || '',
        //     }
        // }
        this.config = config || {};
    }
//deleteFileEndpoint
    // validate(savedData){
    //     return /^\d+$/.test(savedData.id);
    // }

    async uploadFile(file, image) {
        let formData = new FormData();
        formData.append("image", file);

        let loading = image.parentNode.querySelector('.loading')

        const axios = require('axios').default;

        axios({
            method: 'post',
            url: this.config.endpoint,
            data: formData,
            onUploadProgress: function (progressEvent) {
                let progress = Math.floor(progressEvent.loaded / progressEvent.total * 100)
                loading.textContent = `${progress}%`
                if (progressEvent.loaded === progressEvent.total) {
                    loading.remove()
                }
            },
        }).then(function (response) {
            image.src = response.data.file.url
            image.dataset.url = response.data.file.url
            image.dataset.filePath = response.data.file.filePath
            image.dataset.type = response.data.file.type
        });
    }

    render() {
        this.wrapper = document.createElement('div');
        this.wrapper.classList.add('ejs-images-container');

        let buttonsContainer = document.createElement('div');
        buttonsContainer.classList.add('ejs-images-buttons');

        let imagesContainer = document.createElement('div');
        imagesContainer.classList.add('ejs-images-images');

        let captionContainer = document.createElement('div');
        captionContainer.classList.add('caption');

        let linkContainer = document.createElement('div');
        linkContainer.classList.add('link');

        let wrapper = this.wrapper

        if (this.data.file) {
            let imageContainer = document.createElement('div');
            imageContainer.classList.add('ejs-image');

            // let deleteButton = document.createElement('a');
            // deleteButton.classList.add('delete')
            // deleteButton.textContent = "[x]"
            // deleteButton.href = ""
            //
            // deleteButton.addEventListener('click', (e) => {
            //     e.preventDefault()
            //     const axios = require('axios').default;
            //
            //     let formData = new FormData();
            //     formData.append("file", i.filePath);
            //
            //     axios.post(this.config.deleteFileEndpoint, formData).then(function (response) {
            //         if (response.data.success === true) {
            //             imageContainer.remove()
            //         }
            //     })
            // })

            let img = document.createElement('img');
            img.src = this.data.file.url
            img.dataset.url = this.data.file.url
            img.dataset.filePath = this.data.file.filePath
            img.dataset.type = this.data.file.type || ''
            imageContainer.appendChild(img)
            // imageContainer.appendChild(deleteButton)
            imagesContainer.appendChild(imageContainer)
        }



        let captionInput = document.createElement('input');
        captionInput.classList.add('form-control')
        captionInput.value = this.data.caption || ''
        captionContainer.appendChild(captionInput)

        let linkInput = document.createElement('input');
        linkInput.classList.add('form-control')
        linkInput.placeholder = 'Ссылка'
        linkInput.value = this.data.link || ''
        linkContainer.appendChild(linkInput)


        let input = document.createElement('input');
        input.multiple = true
        input.id = "display-image"
        input.type = "file"
        input.accept = "image/jpeg, image/png, image/jpg, application/pdf"

        let _ = this

        input.addEventListener("change", function () {

            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                let img = document.createElement('img');

                let imageContainer = document.createElement('div');
                imageContainer.classList.add('ejs-image');


                let loading = document.createElement('div');
                loading.textContent = "0%"
                loading.classList.add('loading');
                imageContainer.appendChild(loading)

                reader.addEventListener("load", () => {
                    img.src = `${reader.result}`
                    wrapper.appendChild(imageContainer)
                    imageContainer.appendChild(img)
                    imagesContainer.appendChild(imageContainer)
                    _.uploadFile(file, img).then(r => console.log(r,'r'))
                });

                reader.readAsDataURL(file);
            });
        });


        buttonsContainer.appendChild(input);
        this.wrapper.appendChild(buttonsContainer);
        this.wrapper.appendChild(imagesContainer);
        this.wrapper.appendChild(captionContainer);
        this.wrapper.appendChild(linkContainer);
        //
        return this.wrapper;
    }

    save(blockContent) {
        let result = {}
        result.file = {}

        result.file.url = blockContent.querySelector('.ejs-image img')?.dataset.url
        result.caption = blockContent.querySelector('.caption input')?.value
        result.link = blockContent.querySelector('.link input')?.value
        return result
    }

    removed() {
        console.log('removed:')

        this.wrapper.querySelectorAll('.ejs-image img')?.forEach(element => {
            const axios = require('axios').default;

            let formData = new FormData();
            formData.append("file", element.dataset.filePath);

            axios.post(this.config.deleteFileEndpoint, formData).then(function (response) {
                console.log(response)
            })
        })
    }

    renderSettings(){
        const settings = [
            {
                name: 'withBorder',
                icon: `<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.8 10.592v2.043h2.35v2.138H15.8v2.232h-2.25v-2.232h-2.4v-2.138h2.4v-2.28h2.25v.237h1.15-1.15zM1.9 8.455v-3.42c0-1.154.985-2.09 2.2-2.09h4.2v2.137H4.15v3.373H1.9zm0 2.137h2.25v3.325H8.3v2.138H4.1c-1.215 0-2.2-.936-2.2-2.09v-3.373zm15.05-2.137H14.7V5.082h-4.15V2.945h4.2c1.215 0 2.2.936 2.2 2.09v3.42z"/></svg>`
            },
            {
                name: 'stretched',
                icon: `<svg width="17" height="10" viewBox="0 0 17 10" xmlns="http://www.w3.org/2000/svg"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>`
            },
            {
                name: 'withBackground',
                icon: `<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.043 8.265l3.183-3.183h-2.924L4.75 10.636v2.923l4.15-4.15v2.351l-2.158 2.159H8.9v2.137H4.7c-1.215 0-2.2-.936-2.2-2.09v-8.93c0-1.154.985-2.09 2.2-2.09h10.663l.033-.033.034.034c1.178.04 2.12.96 2.12 2.089v3.23H15.3V5.359l-2.906 2.906h-2.35zM7.951 5.082H4.75v3.201l3.201-3.2zm5.099 7.078v3.04h4.15v-3.04h-4.15zm-1.1-2.137h6.35c.635 0 1.15.489 1.15 1.092v5.13c0 .603-.515 1.092-1.15 1.092h-6.35c-.635 0-1.15-.489-1.15-1.092v-5.13c0-.603.515-1.092 1.15-1.092z"/></svg>`
            }
        ];
        const wrapper = document.createElement('div');

        settings.forEach( tune => {
            let button = document.createElement('div');

            button.classList.add('cdx-settings-button');
            button.innerHTML = tune.icon;
            wrapper.appendChild(button);
        });

        return wrapper;
    }
}
