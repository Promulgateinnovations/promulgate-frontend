$(document).ready(function () {

    const GoogleDrivePickerClassInstance = new GoogleDrivePickerClass();

    $('.open-google-drive').click(function () {

        if (GOOGLE_DRIVE_ACCESS_TOKEN) {

            GoogleDrivePickerClassInstance.init($(this).attr('data-channel-source'));

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Digital Assess Management',
                text: 'You haven\'t connected your Google drive account',
                confirmButtonText: 'Connect Now'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.href = BUSINESS_URL;
                }
            })
        }

    })

});


class GoogleDrivePickerClass {

    constructor() {

        this.pickerApiLoaded = false;
        this.AccessToken = '';
    }

    init = (channel_name) => {

        this.channelName = channel_name || '';
        gapi.load('auth', {'callback': this.onauthApiLoad});
    }

    setAuthCode = (accessToken) => {
        console.log(accessToken);
        this.AccessToken = accessToken;
    };


    onauthApiLoad = () => {

        this.setAuthCode(GOOGLE_DRIVE_ACCESS_TOKEN);
        gapi.load('picker', {
            'callback': this.onPickerApiLoad
        });
    };

    onPickerApiLoad = () => {

        this.pickerApiLoaded = true;
        this.createPicker();
    };

    // Create and render a Picker object for searching images.
    createPicker = () => {

        if (this.pickerApiLoaded && this.AccessToken) {

            var VIEW = new google.picker.View(google.picker.ViewId.DOCS_IMAGES_AND_VIDEOS);
            VIEW.setMimeTypes("image/png,image/jpeg,image/jpg,video/mp4,video/x-flv,video/quicktime");

            // var DOCS_VIEW = new
            // google.picker.DocsView(google.picker.ViewId.FOLDERS);
            // DOCS_VIEW.setIncludeFolders(true); DOCS_VIEW.setStarred(true);

            var DOCS_UPLOAD_VIEW = new google.picker.DocsUploadView();
            DOCS_UPLOAD_VIEW.setIncludeFolders(true);

            var picker = new google.picker.PickerBuilder()
                .enableFeature(google.picker.Feature.SUPPORT_DRIVES)
                .enableFeature(google.picker.Feature.SIMPLE_UPLOAD_ENABLED)
                .enableFeature(google.picker.Feature.SUPPORT_DRIVES)
                .setAppId(GOOGLE_APP_ID)
                .setOAuthToken(this.AccessToken)
                .addView(VIEW)
                .addView(DOCS_UPLOAD_VIEW)
                .setDeveloperKey(GOOGLE_DRIVE_API_KEY)
                .setCallback(this.pickerCallback)
                .setMaxItems(1)
                .setTitle('Pick Image/Video you want to use')
                .build();
            picker.setVisible(true);
        }
    };

    pickerCallback = (data) => {
        console.log(this.AccessToken);
        console.log(data);
        if (data.action == google.picker.Action.PICKED) {

            if (this.channelName) {
                $('#curation_channel_' + this.channelName + ' #file_url').val(data.docs[0].url);
            }
        }
    }

}

// https://www.googleapis.com/drive/v2/files/1IFFXAhmMc3IuLT6c8vXvotvVF490DrQZ?alt=media


// /$oAuthToken =
// 'ya29.XXXXXXXXXXXXXXXXXXXXXXXXX-XXXXXXXXX-XXXXXXX-XXXXXX-X-XXXXXXXXXXXX-XXXX';
// $fileId = '0B4zzcXXXXXXXXXXXXXXXXXXXXXX';  $getUrl =
// 'https://www.googleapis.com/drive/v2/files/' . $fileId . '?alt=media';
// $authHeader = 'Authorization: Bearer ' . $oAuthToken ;   $ch =
// curl_init($url);  curl_setopt($ch, CURLOPT_HEADER, 0); curl_setopt($ch,
// CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); curl_setopt($ch,
// CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,
// false);  curl_setopt($ch, CURLOPT_HTTPHEADER, [$authHeader]);  $data =
// curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $error =
// curl_errno($ch);   $data = curl_exec($ch); $error = curl_error($ch); curl_close($ch);  file_put_contents("destination-file.jpg", $data);