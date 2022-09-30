export default class {
    static videoThumbnail = url => {
        let videoId = this.youtube_parser(url);
        return `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`;
    };
    static videoUrl = url => {
        let videoId = this.youtube_parser(url);
        return `https://www.youtube.com/embed/${videoId}`;
    };
    static youtube_parser = url => {
        let regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        let match = url.match(regExp);
        return (match && match[7].length==11)? match[7] : false;
    };
}
