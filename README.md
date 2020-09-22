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
    - [Assets Path](#assets-path)
    - [Pictures Path](#pictures-path)
    - [Downloading Images](#downloading-images)
      - [Download Images](#download-images)
      - [Allow Unsafe sources](#allow-unsafe-sources)
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

Additionally to automatically scaling the pictures in the `content` part of the page, it also parses any pictures in the header that is defined in the meta part as `thumbnail`

## Configuration
Add these to your `config.yml` to make this Plugin work with your own installation. What you see here are the default values

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
By default, all pictures in the `content` part of the page will be in the size 1000x[calculated height] 

Right now there is no way to change this, so the size `1000: 1000` is required, otherwise your site will break.

Pictures that are defined in `thumbnail` will be available to you in your `.twig` templates with the variable `{{ thumbnail.[size name] }}` 

### Assets Path
This Variable name can be a bit confusing, as it's actually the path on the server where the relative asset path points to.

So, if your `custom.css` file is linked in your head tag to `/assets/custom.css`, on the server it should be stored under `/var/www/html/assets/custom.css`

```
absolute_assets_path: '/var/www/html'
```

### Pictures Path
This is the path on your server where your pictures are stored
```
base_pictures_path: '/var/www/html/assets/pictures'
```

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

<b>Important</b>: Is this set to false and you do try to display a file that isn't served over https, the page will break!
```
allow_unsafe_sources: false
```

## Versions
The first 3 digits in the Pico Version the Plugin has been made for (So Version tagged 2.1.1 is made for for Pico Version 2.1) 

The last digit of the Plugin's Version refers to the patch number of the Plugin itself