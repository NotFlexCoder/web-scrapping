# 🌐 Web Scrapping Api

This PHP script allows users to download a zip archive containing resources (such as images, CSS, JavaScript, etc.) from a provided URL. The script fetches the HTML of the given URL, extracts all resources (images, CSS, JS files), and bundles them into a downloadable zip file.

## 🚀 Features

- 🔗 Accepts a URL as input via a GET request.
- ✅ Validates the provided URL.
- 🖼️ Extracts all images, CSS, JavaScript, and other resources linked in the HTML.
- 📥 Downloads the resources and stores them in a zip file.
- 📂 Supports immediate download of the zip file or storing it on the server for future access.
- 🔄 Outputs a JSON response for easy integration with other applications.

## 🛠️ Requirements

- PHP version 7.4 or higher.
- `ZipArchive` PHP extension enabled.
- `cURL` PHP extension enabled.

## 📡 Usage

1. **Setup**: Upload the `index.php` file to your web server.
2. **Access**: Send a GET request to the script with the URL parameter: https://yourdomain.com/index.php?url=https://example.com
3. **Response**:
- ✅ If the URL is valid and the resources are fetched successfully, the server will respond with the zip file for download.
- 🔒 If `download_file` is set to `true`, the file will be saved in a local directory (`downloads/`) and a link to the downloaded file will be provided in the response.

Example JSON response:

```json
{
  "status": "success",
  "url": "https://yourdomain.com/downloads/example_com.zip"
}
```

If the script encounters an error, the response will include a relevant error message:

```json
{
  "status": "error",
  "message": "Invalid URL"
}
``` 

## 📝 Parameters

- url (required): The URL of the website from which you want to download resources.
- Example: https://example.com

## 🔍 Code Explanation

- The script accepts the URL via a GET request.
- It validates the URL format using filter_var and checks if the URL is valid.
- The script uses cURL to fetch the HTML content of the page.
- It then uses regular expressions to extract all linked resources (images, CSS, JavaScript).
- The resources are downloaded and added to a zip file.
- The zip file is either returned directly for immediate download or saved on the server for later use, depending on the value of $download_file.

## ⚠️ Error Handling

- 🚫 Invalid or missing URL: The script returns an error message indicating that the URL is not provided or is invalid.
- 🛠️ cURL failure: If the script encounters a cURL error while fetching the page, it will return an error message.
- 🗃️ Unable to create ZIP: If the script fails to create a ZIP file, it will return an error message.

## 🔧 Customization
$download_file: Set this variable to true if you want the zip file to be saved to the server. By default, it’s set to false, which means the file will be downloaded immediately.

```json
$download_file = true;
```

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/NotFlexCoder/web-scrapping/blob/main/LICENSE) file for details.
