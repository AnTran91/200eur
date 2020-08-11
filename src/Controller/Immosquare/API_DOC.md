# Immosquare (API)

This guide will show you how to use the Immosquare (API).

## Create an order

**URL** : `/api/create/order/`

**Method** : `POST`

**Auth required** : YES

**Data constraints**

- `delivery_time` : (enum) [24_hours | 48_hours] Delivery Time
- `images` : (array) [array of image object] List of images

- `url` : (string) [1 to 255 chars] Image URL
- `service` : (enum) [CIEL_BLEU | PERTINENCE_360 | PHOTO_STAGING_2D | PHOTO_STAGING_VIRTUEL | EQUILIBRAGE | PERTINENCE_INTERIEUR | PERTINENCE_EXTERIEUR | AMENAGEMENT_EXTERIEUR | AMBIANCE_DE_CREPUSCULE]

- **[if service=CIEL_BLEU] `settings :`**
```json
{
       "origin_size": "(enum) [original | custom] Resize the image or Keep the dimensions of the imported image",
       "height": "(float) height of the resized image" ,
       "Width": "(float) width of the resized image",
       "logo": "(enum) [yes | no] Do you want to insert a logo?",
       "logo_file": "(string) logo URL",
       "logo_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
       "gif_animation": "(enum) [yes | no] Would you like an animated GIF before-after of your image?",
       "gif_animation_config": "(enum) [original | custom] From this image or Personalize",
       "gif_animation_watermark": "(string) [1 to 255 chars] Type your personalized text",
       "gif_animation_logo_file": "(string) Put your personalized logo URL",      
       "gif_animation_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
       "watermark": "(enum) [yes | no] Do you want to insert a watermark?",
       "watermark_text": "(string) [1 to 255 chars] Type your watermark",
       "watermark_logo_file": "(string) Put your personalized logo URL",
       "watermark_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the position of the watermark"
}
```

- **[if service=PHOTO_STAGING_2D] `settings :`**
```json
{
      "origin_size": "(enum) [original | custom] Resize the image or Keep the dimensions of the imported image",
      "height": "(float) height of the resized image" ,
      "Width": "(float) width of the resized image",
      "logo": "(enum) [yes | no] Do you want to insert a logo?",
      "logo_file": "(string) logo URL",
      "logo_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "gif_animation": "(enum) [yes | no] Would you like an animated GIF before-after of your image?",
      "gif_animation_config": "(enum) [original | custom] From this image or Personalize",
      "gif_animation_watermark": "(string) [1 to 255 chars] Type your personalized text",
      "gif_animation_logo_file": "(string) Put your personalized logo URL",      
      "gif_animation_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "watermark": "(enum) [yes | no] Do you want to insert a watermark?",
      "watermark_text": "(string) [1 to 255 chars] Type your watermark",
      "watermark_logo_file": "(string) Put your personalized logo URL",
      "watermark_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the position of the watermark",
      "commentary": "(string) [1 to 255 chars] If you have any specific instructions to send us, write here",
      "renovation_proposal": "(enum) [yes | no] Non-contractual renovation proposal.",
      "renovation_project": "(enum) [yes | no] Non-contractual renovation project.",
      "field_renovation": ""
}
```

- **[if service=PERTINENCE_INTERIEUR] `settings :`**
```json
{
      "origin_size": "(enum) [original | custom] Resize the image or Keep the dimensions of the imported image",
      "height": "(float) height of the resized image" ,
      "Width": "(float) width of the resized image",
      "logo": "(enum) [yes | no] Do you want to insert a logo?",
      "logo_file": "(string) logo URL",
      "logo_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "gif_animation": "(enum) [yes | no] Would you like an animated GIF before-after of your image?",
      "gif_animation_config": "(enum) [original | custom] From this image or Personalize",
      "gif_animation_watermark": "(string) [1 to 255 chars] Type your personalized text",
      "gif_animation_logo_file": "(string) Put your personalized logo URL",      
      "gif_animation_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "watermark": "(enum) [yes | no] Do you want to insert a watermark?",
      "watermark_text": "(string) [1 to 255 chars] Type your watermark",
      "watermark_logo_file": "(string) Put your personalized logo URL",
      "watermark_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the position of the watermark",
      "commentary": "(string) [1 to 255 chars] If you have any specific instructions to send us, write here",
      "make_bed": "(enum) [yes | no] Would you like to make the bed?",
      "blue_sky": "(enum) [yes | no] Would you like a blue sky in season ?",
      "document": "(string) Put your personalized document URL"
}
```

- **[if service=PERTINENCE_EXTERIEUR] `settings :`**
```json
{
      "origin_size": "(enum) [original | custom] Resize the image or Keep the dimensions of the imported image",
      "height": "(float) height of the resized image" ,
      "Width": "(float) width of the resized image",
      "logo": "(enum) [yes | no] Do you want to insert a logo?",
      "logo_file": "(string) logo URL",
      "logo_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "gif_animation": "(enum) [yes | no] Would you like an animated GIF before-after of your image?",
      "gif_animation_config": "(enum) [original | custom] From this image or Personalize",
      "gif_animation_watermark": "(string) [1 to 255 chars] Type your personalized text",
      "gif_animation_logo_file": "(string) Put your personalized logo URL",      
      "gif_animation_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "watermark": "(enum) [yes | no] Do you want to insert a watermark?",
      "watermark_text": "(string) [1 to 255 chars] Type your watermark",
      "watermark_logo_file": "(string) Put your personalized logo URL",
      "watermark_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the position of the watermark",
      "commentary": "(string) [1 to 255 chars] If you have any specific instructions to send us, write here",
      "blue_sky": "(enum) [yes | no] Would you like a blue sky in season ?",
      "document": "(string) Put your personalized document URL"
}
```

- **[if service=EQUILIBRAGE] `settings :`**
```json
{
      "origin_size": "(enum) [original | custom] Resize the image or Keep the dimensions of the imported image",
      "height": "(float) height of the resized image" ,
      "Width": "(float) width of the resized image",
      "logo": "(enum) [yes | no] Do you want to insert a logo?",
      "logo_file": "(string) logo URL",
      "logo_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "gif_animation": "(enum) [yes | no] Would you like an animated GIF before-after of your image?",
      "gif_animation_config": "(enum) [original | custom] From this image or Personalize",
      "gif_animation_watermark": "(string) [1 to 255 chars] Type your personalized text",
      "gif_animation_logo_file": "(string) Put your personalized logo URL",      
      "gif_animation_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the logo position",
      "watermark": "(enum) [yes | no] Do you want to insert a watermark?",
      "watermark_text": "(string) [1 to 255 chars] Type your watermark",
      "watermark_logo_file": "(string) Put your personalized logo URL",
      "watermark_place": "(enum) [tl|tm|tr|ml|mm|mr|bl|bm|br] Choose the position of the watermark"
}
```


**Header constraints**

The Access Token `X-AUTH-TOKEN` used as a Bearer credential and transmitted in an HTTP Authorization header to fetch user.

```
X-AUTH-TOKEN: [user token]
```

**Data examples**

```json
{
	"delivery_time": "24_hours",
	"images": [
		{
			"url": "https://s3.amazonaws.com/pixis.io/...",
			"service": "AMBIANCE_DE_CREPUSCULE",
			"settings": {
				  "origin_size": "custom",
                  "height": "100" ,
                  "Width": "150",
                  "logo": "yes",
                  "logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "logo_place": "tm",
                  "gif_animation": "yes",
                  "gif_animation_config": "custom",
                  "gif_animation_watermark": "Gif animation watermark",
                  "gif_animation_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",      
                  "gif_animation_place": "tl",
                  "watermark": "yes",
                  "watermark_text": "watermark text",
                  "watermark_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "watermark_place": "bl",
                  "commentary": "specific instructions",
                  "swimming_pool": "no"
			}
		},
		{
            "url": "https://s3.amazonaws.com/pixis.io/...",
            "service": "CIEL_BLEU",
            "settings": {
                  "origin_size": "custom",
                  "height": "100" ,
                  "Width": "150",
                  "logo": "yes",
                  "logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "logo_place": "tm",
                  "gif_animation": "yes",
                  "gif_animation_config": "custom",
                  "gif_animation_watermark": "Gif animation watermark",
                  "gif_animation_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",      
                  "gif_animation_place": "tl",
                  "watermark": "yes",
                  "watermark_text": "watermark text",
                  "watermark_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "watermark_place": "bl"
            }
        }
	]
}
```

**Header examples**

```
X-AUTH-TOKEN: cb6544e7-8e26-449b-b6cd-83dfe684d78f
```

## Success Responses

**Condition** : Data provided is valid and User is Authenticated.

**Code** : `200 OK`

**Content example** : Response will reflect back the updated information

```json
{
    "order_id": 25,
    "created" : "29/10/2018",
	"delivery_time": "24_hours",
	"images": [
		{
			"url": "https://s3.amazonaws.com/pixis.io/...",
			"service": "AMBIANCE_DE_CREPUSCULE",
			"settings": {
				  "origin_size": "custom",
                  "height": "100" ,
                  "Width": "150",
                  "logo": "yes",
                  "logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "logo_place": "tm",
                  "gif_animation": "yes",
                  "gif_animation_config": "custom",
                  "gif_animation_watermark": "Gif animation watermark",
                  "gif_animation_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",      
                  "gif_animation_place": "tl",
                  "watermark": "yes",
                  "watermark_text": "watermark text",
                  "watermark_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "watermark_place": "bl",
                  "commentary": "specific instructions",
                  "swimming_pool": "no"
			}
		},
		{
            "url": "https://s3.amazonaws.com/pixis.io/...",
            "service": "CIEL_BLEU",
            "settings": {
                  "origin_size": "custom",
                  "height": "100" ,
                  "Width": "150",
                  "logo": "yes",
                  "logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "logo_place": "tm",
                  "gif_animation": "yes",
                  "gif_animation_config": "custom",
                  "gif_animation_watermark": "Gif animation watermark",
                  "gif_animation_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",      
                  "gif_animation_place": "tl",
                  "watermark": "yes",
                  "watermark_text": "watermark text",
                  "watermark_logo_file": "https://s3.amazonaws.com/pixis.io/logo/...",
                  "watermark_place": "bl"
            }
        }
	]
}
```

## Error Response

**Condition** : If provided data is invalid, e.g. a url field is not available.

**Code** : `400 BAD REQUEST`

**Content example** :

```json
{
    "status": 400,
    "title": "There was a validation error",
    "errors": {
        "url": [
            {
                "data": "http://httfreedownloads.uniqueteachingresources.com/JPG-Famous-Motivational-Quotes-By-Paul-Brandt-Dont-tell-me-the-skys-the-limit-when-there-are-footprints-on-the-moon.jpg?id=%2210%22",
                "constraints": [
                    "The image file is corrupted."
                ]
            }
        ]
    }
}
```

## Fetch an order

**URL** : `/api/fetch/{ID}/order`

**ID** : Order identifier

**Method** : `GET`

**Auth required** : YES

**Data constraints**

Not Data required

**Header constraints**

The Access Token `X-AUTH-TOKEN` used as a Bearer credential and transmitted in an HTTP Authorization header to fetch user.

```
X-AUTH-TOKEN: [user token]
```

## Success Responses

**Condition** : Order is ready and User is Authenticated.

**Code** : `200 OK`

**Content example** : Response will reflect back the updated information

```json
{
    "identify": 463,
    "delivery_time": "24_hours",
    "order_number": 373,
    "order_creation_date": "2018-11-12T14:45:37+01:00",
    "order_delivery_date": "2018-11-12T00:00:00+01:00",
    "images": [
        {
            "origin_image_URL": "https://cdn.shopifycloud.com/hatchful-web/assets/2adcef6c1f7ab8a256ebdeba7fceb19f.png",
            "CIEL_BLEU": {
                "retouched_image_URL": "/uploads/files/dc8a0dc6-b4ce-4512-9450-907ba21325fd/83f9c489-b568-491f-8ccb-74f25e1106f6//done/53c62a70-01ff-46df-aefd-b7441485cd4e/Screenshot from 2018-08-09 16-07-35.png"
            }
        },
        {
            "origin_image_URL": "http://httfreedownloads.uniqueteachingresources.com/JPG-Famous-Motivational-Quotes-By-Paul-Brandt-Dont-tell-me-the-skys-the-limit-when-there-are-footprints-on-the-moon.jpg?id=%2210%22",
            "CIEL_BLEU": {
                "retouched_image_URL": "/uploads/files/dc8a0dc6-b4ce-4512-9450-907ba21325fd/83f9c489-b568-491f-8ccb-74f25e1106f6//done/b09522d6-055d-4505-b71d-841158051305/23466317216_b99485ba14_o-panorama.jpg"
            }
        },
        {
            "origin_image_URL": "https://steamcdn-a.akamaihd.net/steam/apps/346110/capsule_616x353.jpg?id=55",
            "CIEL_BLEU": {
                "retouched_image_URL": "/uploads/files/dc8a0dc6-b4ce-4512-9450-907ba21325fd/83f9c489-b568-491f-8ccb-74f25e1106f6//done/c939e31d-b85e-41c0-a86e-aedb3472dfb0/Screenshot from 2018-06-22 17-14-09.png"
            }
        }
    ],
    "total_amount": "72€"
}
```

## Error Response

**Condition** : If order is not ready.

**Code** : `400 BAD REQUEST`

**Content example** :

```json
{
    "status": 400,
    "title": "The requested order is not ready",
    "current_status": "En Production"
}
```

## That was it!
