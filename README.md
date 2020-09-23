# Pico Image Processor Plugin

<b>Table of Contents</b>
- [Pico Image Processor Plugin](#pico-image-processor-plugin)
  - [Installation](#installation)
    - [Git](#git)
    - [Just the file](#just-the-file)
  - [What does this Plugin do?](#what-does-this-plugin-do)
  - [How can you use this Plugin?](#how-can-you-use-this-plugin)
  - [Configuration](#configuration)
    - [Sizes](#sizes)
    - [Absolute Web Directory](#absolute-web-directory)
    - [Relative Pictures Path](#relative-pictures-path)
    - [Relative Scaled Image Path](#relative-scaled-image-path)
    - [Downloading Images](#downloading-images)
      - [Download Images](#download-images)
      - [Allow Unsafe sources](#allow-unsafe-sources)
      - [Download Path](#download-path)
    - [Example](#example)
  - [Versions](#versions)

## Installation
### Git
Clone this repository into your `plugins` directory (`git clone https://github.com/christiangroeber/PicoImageProcessor.git`)

### Just the file
Download the file `PicoImageProcessor.php` to your `plugins` directory 

## What does this Plugin do?
This Plugin takes existing Images and scales them down to your desired size.

By using this Plugin you can help users with a limited bandwidth, and increase your pages load speeds.

## How can you use this Plugin?
After installing this Plugin it automatically scales all of your images that are inside your pages `content` down to your desired sizes when first requesting said page.

This means that all pictures mentioned in the main part of any requested page will be scaled down to the sizes mentioned in your config file (read [This section](#sizes) for more information on that)

Additionally, to automatically scale the pictures in the `content` part of the page, it also parses any pictures in the header that are defined in the meta part as `thumbnail`

## Configuration
Add these to your `config.yml` to make this Plugin work with your own installation. What you see here are the default values.

All options that you wish to change need to be in the `config.yaml` in the following format:
```
PicoImageProcessor:
    [variable name]: [variable value]
```

### Sizes
Array in the format of `[size name]: [size in pixels]`

The different sizes you want your pictures to be available in
```
sizes:
    thumb: 200
    400: 400
    500: 500
    1000: 1000
    max: 1080
```
By default, all pictures in the `content` part of the page will be in the size `1000x[calculated height]` 

Right now there is no way to change this, so the size `1000: 1000` is required, otherwise your site will break.

Pictures that are defined in `thumbnail` will be available to you in your `.twig` templates with the variable `{{ thumbnail.[size name] }}` 

### Absolute Web Directory
Type: `string`

This is the path from where your website is being served, usually under `/var/www/html` 

```
absoluteWebDirectory: '/var/www/html'
```

### Relative Pictures Path
Type: `string`

This is the path on your server where your pictures. It is relative to the [Absolute Web Directory](#absolute-web-directory).
This is intended to be the path where you store your pictures. It doesn't matter to the Plugin's functionality if your pictures are in a sub-folder of that directory.
```
relativePicturesPath: '/assets/pictures'
```

So on the server, this will point to `/var/www/html/assets/pictrues`

### Relative Scaled Image Path
Type: `string`

This is where your scaled images will be stored. 

<b>Important</b>: Please make sure that the web-user has read and write access on this directory. Usually, the web-user is www-data

```
relativeScaledImagePath: '/assets/thumbs'
```

On your server, this will point to `/var/www/html/assets/thumbs`

### Downloading Images
This Plugin does support the downloading of images to the server.

#### Download Images
Type: `boolean`

Should this Plugin be able to download pictures from online sources? Should this be set to false it will just use the link provided 
```
download_image: true
```

#### Allow Unsafe sources
Type: `boolean`

Allow downloading images that aren't served with a secure connection (the url begins with `http://`)

<b>Important</b>: If this set to false and you do try to display a file that isn't served over https, the page will break!
```
allow_unsafe_sources: false
```

#### Download Path
Type: `string`

The directory, in which your images should be downloaded.

<b>Important</b>: Please make sure that the web-user has read and write access on this directory. Usually, the web-user is www-data

```
relativeDownloadsPath: '/assets/downloads'
```

On the server, this will point to `/var/www/html/assets/thumbs`

### Example
This is the default configuration of this plugin. Feel free to copy the following code block into your `config.yaml` and make any changes you wish to make.

```
PicoImageProcessor:
    sizes:
        thumb: 200
        400: 400
        500: 500
        1000: 1000
        max: 1080
    absoluteWebDirectory: '/var/www/html'
    relativePicturesPath: '/assets/pictures'
    relativeScaledImagePath: '/assets/thumbs'
    relativeDownloadPath: '/assets/downloads'
    downloadImages: true
    allowUnsafeSources: false
```

## Versions
The first 2 digits in the Pico Version the Plugin has been made for (So Version tagged 2.1.1 is made for for Pico Version 2.1) 

The last digit of the Plugin's Version refers to the patch number of the Plugin itself