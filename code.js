<script>
javascript: 
function getFile(txt,search) {
if (txt.indexOf(search) !== -1) {
return search + txt.split(search)[1].split("\"")[0];
}
}
function encode(str) {
var result = "";
for (i = 0; i < str.length; i++) {
if (str.charAt(i) == " ") result += "+";
else result += str.charAt(i);
}

return escape(result);
}
var markup = "http://www.sockshare.com/file/749923043C493EA5";
url = getFile(markup,'/get_file.php?id=');
url2 = encode (url);
url1 = getFile(markup,'/static/previews/');
url22 = encode (url1);

window.location = "http://cinema.carr-home.dyndns.org/video.php?link=http%3A//"+ window.location.host + url2  + "&screen=http%3A//static1."+window.location.host.substr(4)+ url22;
</script>