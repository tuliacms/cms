class UploadInstance {
    endpoint;
    progressbar;

    totalSize = 0;
    files = [];
    uploadedFiles = [];

    constructor (endpoint, progressbar) {
        this.endpoint = endpoint;
        this.progressbar = progressbar;
    }

    upload (files, directory) {
        let self = this;
        this.progressbar.call(null, 0);

        this.files = [];
        this.uploadedFiles = [];

        for (let i = 0; i < files.length; i++) {
            this.totalSize += files[i].size;
            this.files.push({
                name: files[i].name,
                size: files[i].size,
                loaded: 0
            });
        }

        FileAPI.upload({
            url: (this.endpoint)('upload'),
            data: {
                directory: directory,
            },
            files: {
                file: files
            },
            progress: function (evt, file) {
                self.progressbar.call(null, self.calculateLoadedSize(evt, file) / self.totalSize * 100);
            },
            complete: function (err, xhr, file) {
                let json = JSON.parse(xhr.responseText);
                self.progressbar.call(null, 100, self.uploadedFiles);
                self.reset();

                if(json && json.status === 'error') {
                    swal({
                        title: 'Nie udało się :(',
                        text: json.message,
                        type: 'error'
                    });
                }
            },
            filecomplete: function (err, xhr, file) {
                let json = JSON.parse(xhr.responseText);

                for (let i in json.uploaded_files) {
                    self.uploadedFiles.push(json.uploaded_files[i]);
                }
            }
        });
    }

    calculateLoadedSize (event, file) {
        let loaded = 0;

        for(let i = 0; i < this.files.length; i++) {
            if(this.files[i].name === file.name) {
                this.files[i].loaded = event.loaded;
            }

            loaded += this.files[i].loaded;
        }

        return loaded;
    };

    reset () {
        this.totalSize = 0;
    };
}

export default class {
    endpoint;

    constructor (endpoint) {
        this.endpoint = endpoint;

        if(! window.FileAPI) {
            console.error('FileAPI extension not loaded.');
            return;
        }
    }

    upload (files, directory, progressbar) {
        return (new UploadInstance(this.endpoint, progressbar)).upload(files, directory);
    };
};
