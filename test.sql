START TRANSACTION;
INSERT INTO `photo_retouching_pricing` (id,retouch_id,order_delivery_time_id,price) VALUES (1,6,1,9),
 (2,6,2,13.5),
 (3,7,1,8.5),
 (4,7,2,12.75),
 (5,1,1,24),
 (6,1,2,36),
 (7,8,3,70),
 (8,3,1,2.5),
 (9,3,2,5),
 (10,2,1,13),
 (11,2,2,19.5),
 (12,5,3,75),
 (13,0,1,5),
 (14,0,2,7.5),
 (15,9,1,7.5),
 (16,9,2,11.25),
 (17,10,1,13),
 (18,10,2,15),
 (19,11,1,12),
 (20,11,2,18),
 (21,12,4,9),
 (22,12,5,13.5),
 (23,13,5,8.5),
 (24,13,4,12.75),
 (25,14,4,13),
 (26,14,5,19.5),
 (27,15,4,2.5),
 (28,15,5,5);
INSERT INTO `order_delivery_time` (id,global,selected_by_default,time,unit,order_delivery_code,app_type) VALUES (1,1,1,48,'time.hour',NULL,'application.type.emmobilier'),
 (2,1,0,24,'time.hour',NULL,'application.type.emmobilier'),
 (3,0,0,1,'time.week',NULL,'application.type.emmobilier'),
 (4,1,0,24,'time.hour','24_hours','application.type.immosquare'),
 (5,1,0,48,'time.hour','48_hours','application.type.immosquare');
INSERT INTO `field_renovation_type` (id,type_name) VALUES (1,'Mur'),
 (2,'Sol');
INSERT INTO `field_renovation_choices` (id,type_id,picture_path,uuid) VALUES (1,1,'/uploads/images/renovation_choices/28968a09-5eca-44c9-b986-da4678fdbe16/gris.png','28968a09-5eca-44c9-b986-da4678fdbe16'),
 (2,1,'/uploads/images/renovation_choices/71cdeafb-dfaa-4eb7-bc30-6423fe74acd6/mur1.jpg','71cdeafb-dfaa-4eb7-bc30-6423fe74acd6'),
 (3,1,'/uploads/images/renovation_choices/e6214395-8f30-4b39-a342-40aea9841fd4/mur2.jpg','e6214395-8f30-4b39-a342-40aea9841fd4'),
 (4,1,'/uploads/images/renovation_choices/1b8753b6-0db3-4660-ae95-136daa503311/mur3.jpg','1b8753b6-0db3-4660-ae95-136daa503311'),
 (5,1,'/uploads/images/renovation_choices/20e39600-493d-4170-b8f5-2dd566bf7c62/grege.png','20e39600-493d-4170-b8f5-2dd566bf7c62'),
 (10,2,'/uploads/images/renovation_choices/157a653c-e900-42d4-bf7f-472daec659ed/Conserver-le-sol-d''origine.jpg','157a653c-e900-42d4-bf7f-472daec659ed'),
 (11,2,'/uploads/images/renovation_choices/75e3ad03-f23e-4671-86c8-671def1cdf34/carrelage2.jpg','75e3ad03-f23e-4671-86c8-671def1cdf34'),
 (12,2,'/uploads/images/renovation_choices/00a3847b-c918-4308-aa85-28f9d62659ee/carrelage3.jpg','00a3847b-c918-4308-aa85-28f9d62659ee'),
 (14,2,'/uploads/images/renovation_choices/e264f508-48fd-4128-82a7-72962d2b90f4/carrelage1.jpg','e264f508-48fd-4128-82a7-72962d2b90f4'),
 (16,2,'/uploads/images/renovation_choices/f6f37ba4-0fbe-4b28-b4ff-2f90ac9b97ec/moquette2.jpg','f6f37ba4-0fbe-4b28-b4ff-2f90ac9b97ec'),
 (17,2,'/uploads/images/renovation_choices/30a32480-d2e5-4d83-8fd6-2dab1f1010ff/moquette1.jpg','30a32480-d2e5-4d83-8fd6-2dab1f1010ff'),
 (18,2,'/uploads/images/renovation_choices/e5dcc042-49fc-45e9-a2f4-a2a151a37cf2/moquette3.jpg','e5dcc042-49fc-45e9-a2f4-a2a151a37cf2'),
 (19,2,'/uploads/images/renovation_choices/4f0de90d-7e37-4181-bb22-f313873ed57f/parquet3.jpg','4f0de90d-7e37-4181-bb22-f313873ed57f'),
 (20,2,'/uploads/images/renovation_choices/1999bc25-39f1-4f30-98ba-1e863cb5436f/parquet2.jpg','1999bc25-39f1-4f30-98ba-1e863cb5436f'),
 (21,2,'/uploads/images/renovation_choices/cb019e0b-08d3-41ec-8759-f629949264c4/parquet1.jpg','cb019e0b-08d3-41ec-8759-f629949264c4'),
 (22,1,'/uploads/images/renovation_choices/cc726c3c-989a-4376-b9a2-5b8089f05ce4/lierre.png','cc726c3c-989a-4376-b9a2-5b8089f05ce4'),
 (23,NULL,'/uploads/images/renovation_choices/cc726c3c-989a-4376-b9a2-5b8089f05ce4/lierre.png','cc726c3c-989a-4376-b9a2-5b8089f05ce4'),
 (24,1,'/uploads/images/renovation_choices/6b7c06fd-2e22-4556-b16b-d1f5e680a1d2/Conserverlemurdorigine.jpg','6b7c06fd-2e22-4556-b16b-d1f5e680a1d2');
INSERT INTO `field_group_retouch` (field_group_id,retouch_id) VALUES (7,2),
 (9,8),
 (10,8),
 (10,5),
 (11,5),
 (5,1),
 (12,9),
 (5,2),
 (5,6),
 (5,7),
 (13,2),
 (14,1),
 (14,6),
 (14,7),
 (15,5),
 (15,8),
 (16,6),
 (15,6),
 (15,7),
 (15,1);
INSERT INTO `field_group` (id,label_text,position,order_number) VALUES (1,'Taille','left',1),
 (2,'Format','left',2),
 (3,'Logo','left',3),
 (4,'GIF animé Avant-Après','left',4),
 (5,'Information complémentaire','right',5),
 (6,'Filigrane','right',6),
 (7,'Pour Photo-Staging 2D, besoin d''écrire :','right',7),
 (9,'Pour AMÉNAGEMENT EXTÉRIEUR, besoin d''écrire :','right',7),
 (10,'Cahier des Charges','right',5),
 (11,'Pour Photo-Staging Virtuel, besoin d''écrire :','right',7),
 (12,'Piscine extérieure','right',7),
 (13,'Définissez pour chaque image 1 des choix de rénovation mur et/ou sol, que nous avons pris soin de présélectionner pour vous.','right',9),
 (14,'Ciel bleu','right',10),
 (15,'Besoins particuliers ? Cet espace est dédié à la dépose de visuels et documents nécessaires à la retouche de votre image.','right',7),
 (16,'Lit non fait','right',8);
INSERT INTO `field_field_renovation_type` (field_id,field_renovation_type_id) VALUES (29,1),
 (29,2);
INSERT INTO `field_field_choices` (field_id,field_choices_id) VALUES (1,1),
 (1,2),
 (4,3),
 (4,4),
 (5,5),
 (5,6),
 (5,7),
 (10,8),
 (10,9),
 (5,10);
INSERT INTO `field_details` (id,picture_detail_id,field_id,price) VALUES (2,7,32,2),
 (6,58,32,2),
 (7,66,9,5),
 (8,3,32,2),
 (9,7,30,2.5),
 (10,8,30,2.5),
 (11,17,30,2.5),
 (12,17,32,2),
 (13,47,30,2.5),
 (14,47,32,2),
 (15,52,32,2),
 (16,137,30,2.5),
 (17,350,9,5),
 (18,350,30,2.5),
 (19,357,9,5),
 (20,357,30,2.5),
 (21,357,32,2),
 (22,393,9,5),
 (23,394,9,5),
 (24,467,9,5);
INSERT INTO `field_choices` (id,choice_label,choice_value) VALUES (1,'Conserver les dimensions de l''image importée','original'),
 (2,'Redimensionner l''image','custom'),
 (3,'PX','px'),
 (4,'CM','cm'),
 (5,'Format original','original'),
 (6,'Extension JPEG','jpeg'),
 (7,'Extension PNG','png'),
 (8,'À partir de cette image','original'),
 (9,'Personnaliser','custom'),
 (10,'Extension TIFF','tiff');
INSERT INTO `field` (id,field_group_id,disabled_on_id,name,empty_data,field_type,order_number,disabled,price,add_the_price_when_value_equals_to,label_text,mapped,htmlclass) VALUES (1,1,NULL,'origin_size','original','radio',1,0,NULL,NULL,NULL,1,NULL),
 (2,1,1,'height',NULL,'text',2,1,NULL,NULL,'Hauteur',1,NULL),
 (3,1,1,'Width',NULL,'text',2,1,NULL,NULL,'Largeur',1,NULL),
 (4,1,1,'unit','px','radio',2,1,NULL,NULL,NULL,1,'form-unit'),
 (5,2,NULL,'returned_format',NULL,'choice',1,0,NULL,NULL,'',1,''),
 (6,3,NULL,'logo','no','boolean',1,0,NULL,NULL,'Souhaitez vous insérer votre logo?',1,NULL),
 (7,3,6,'logo_file',NULL,'image',2,1,NULL,NULL,'Choisissez votre Logo',1,NULL),
 (8,3,6,'logo_place','rb','placement',3,1,NULL,NULL,'Choisissez la position du logo',1,NULL),
 (9,4,NULL,'gif_animation','no','boolean',1,0,5,'yes','Souhaitez-vous un GIF animé Avant-Après de votre image?',1,NULL),
 (10,4,9,'gif_animation_config','original','radio',2,1,NULL,NULL,NULL,1,NULL),
 (11,4,10,'gif_animation_watermark',NULL,'text_no_label',3,1,NULL,NULL,'Tapez votre texte personnalisé',1,NULL),
 (12,4,10,'gif_animation_logo_file',NULL,'image',4,1,NULL,NULL,NULL,1,NULL),
 (13,4,10,'gif_animation_place','rb','placement',5,1,NULL,NULL,'Choisissez la position',1,NULL),
 (14,5,NULL,'comment',NULL,'textarea',1,0,NULL,NULL,'Si vous deviez avoir des directives particulières à nous transmettre, écrivez ici :',1,''),
 (15,6,NULL,'watermark','no','boolean',1,0,NULL,NULL,'Souhaitez-vous insérer un filigrane?',1,NULL),
 (16,6,15,'watermark_text',NULL,'text_no_label',2,1,NULL,NULL,'Tapez votre filigrane',1,NULL),
 (17,6,15,'watermark_logo_file',NULL,'image',3,1,NULL,NULL,'Choisissez votre filigrane',1,NULL),
 (18,6,15,'watermark_place','mm','placement',4,1,NULL,NULL,'Choisissez la position du filigrane',1,NULL),
 (19,7,NULL,'renovationProposal','no','boolean',1,0,NULL,NULL,'Proposition de rénovation non contractuelle.',1,NULL),
 (20,7,NULL,'renovationProject','no','boolean',2,0,NULL,NULL,'Projet de rénovation non contractuel.',1,NULL),
 (22,11,NULL,'planningProposal','no','boolean',1,0,NULL,NULL,'Proposition d''aménagement non contractuelle.',1,NULL),
 (23,11,NULL,'planningProject','no','boolean',2,0,NULL,NULL,'Projet d''aménagement non contractuel.',1,NULL),
 (24,9,NULL,'landscapingProposal','no','boolean',1,0,NULL,NULL,'Proposition d''aménagement Extérieur.',1,NULL),
 (25,9,NULL,'landscapingProject','no','boolean',2,0,NULL,NULL,'Projet d''aménagement Extérieur.',1,NULL),
 (26,10,NULL,'specifications',NULL,'textarea',1,0,NULL,NULL,'Décrivez succintement vos directives d''aménagement, 1 ligne par besoin.',1,NULL),
 (28,12,NULL,'piscine','no','boolean',1,0,NULL,NULL,'Voulez-vous éclairer votre piscine ?',1,NULL),
 (29,13,NULL,'field_renovation',NULL,'image_renovation',1,0,NULL,NULL,NULL,1,NULL),
 (30,14,NULL,'blue_sky','no','boolean',1,0,2.5,'yes','Souhaitez-vous un ciel bleu de saison ?',1,NULL),
 (31,15,NULL,'document',NULL,'file',1,0,NULL,NULL,'(formats : word, powerpoint, pdf, Photo)',1,''),
 (32,16,NULL,'bed','no','boolean',1,0,2,'yes','Souhaitez-vous faire le lit?',1,NULL);
INSERT INTO `ext_translations` (id,locale,object_class,field,foreign_key,content) VALUES (1,'en','App\Entity\FieldChoices','choiceLabel','1','Keep the dimensions of the imported image'),
 (2,'en','App\Entity\FieldChoices','choiceLabel','2','Resize the image'),
 (3,'en','App\Entity\FieldChoices','choiceLabel','5','Origine'),
 (4,'en','App\Entity\FieldChoices','choiceLabel','6','JEPEG extension'),
 (5,'en','App\Entity\FieldChoices','choiceLabel','7','PNG extension'),
 (6,'en','App\Entity\FieldChoices','choiceLabel','8','From this image'),
 (7,'en','App\Entity\FieldChoices','choiceLabel','9','Personalize'),
 (8,'en','App\Entity\FieldGroup','labelText','1','Size'),
 (9,'en','App\Entity\FieldGroup','labelText','4','GIF Animated before after'),
 (10,'en','App\Entity\FieldGroup','labelText','5','Additional information'),
 (11,'en','App\Entity\FieldGroup','labelText','6','Watermark'),
 (12,'en','App\Entity\FieldGroup','labelText','7','For 2D Photo-Staging, need to write:'),
 (13,'en','App\Entity\Field','labelText','2','Height'),
 (14,'en','App\Entity\Field','labelText','3','Width'),
 (15,'en','App\Entity\Field','labelText','6','Would you like to insert your logo ?'),
 (16,'en','App\Entity\Field','labelText','7','Choose your Logo'),
 (17,'en','App\Entity\Field','labelText','8','Choose the position of the logo'),
 (18,'en','App\Entity\Field','labelText','9','Would you like an animated GIF before-after of your image?'),
 (19,'en','App\Entity\Field','labelText','11','Tapez votre filigrane'),
 (20,'en','App\Entity\Field','labelText','12','Choose your Logo'),
 (21,'en','App\Entity\Field','labelText','13','Choose the position of the logo'),
 (22,'en','App\Entity\Field','labelText','14','if you had any special instructions to send us, write here:'),
 (23,'en','App\Entity\Field','labelText','15','Do you want to insert a watermark?'),
 (24,'en','App\Entity\Field','labelText','16','Type your watermark'),
 (25,'en','App\Entity\Field','labelText','17','Choose your watermark'),
 (26,'en','App\Entity\Field','labelText','18','Choose the position of the watermark'),
 (27,'en','App\Entity\Field','labelText','19','Non-contractual renovation proposal.'),
 (28,'en','App\Entity\Field','labelText','20','Non-contractual renovation project.'),
 (29,'en','App\Entity\Retouch','title','5','VIRTUAL PHOTO-STAGING'),
 (30,'en','App\Entity\Retouch','description','5','<p>For indoor photos. - Inlay of furniture / creation of Ambiance. - - Delivery delivered by default, to "about 1 week", even if included in 1 order in 48h or 24h.</p>'),
 (31,'en','App\Entity\Retouch','title','6','RELEVANCE « INTERIOR »'),
 (32,'en','App\Entity\Retouch','description','6','<p>For indoor photos.</p><p>Virtual storage and decluttering:</p><p>- We store personal belongings,</p><p>- We unclutter the interior layout,</p><p>- We balance chromie &amp; light,</p><p>- We correct the optical distortions,</p><p>- We recover the external views.</p><p>This service has its limits, it requires to be able to reconstitute the materials, the forms, the style, the design of the furniture ...</p>'),
 (33,'en','App\Entity\Retouch','title','7','RELEVANCE "EXTERIOR"'),
 (34,'en','App\Entity\Retouch','description','7','<p>For outdoor photos.</p><p>Virtual storage and decluttering :</p><p>- We store personal belongings.</p><p>- We unclutter the exterior layout.</p><p>- We balance chromie &amp; light.</p><p>- We correct the optical distortions.</p>'),
 (35,'en','App\Entity\Retouch','title','8','"OUTDOOR" LAYOUT'),
 (36,'en','App\Entity\Retouch','description','8','<p>For outdoor photos.</p><p>- We arrange your outdoor spaces according to your requests,</p><p>- We create non-existent lawns,</p><p>- We decorate your spaces of floral ornamentation, shrubs,</p><p>- We encrust garden furniture,</p><p>- We work the facades of goods,</p>'),
 (37,'en','App\Entity\FieldGroup','labelText','13','For each image 1, define the wall and / or floor renovation choices that we have pre-selected for you.'),
 (38,'en','App\Entity\Field','labelText','29',NULL),
 (39,'en','App\Entity\FieldRenovationType','typeName','1','Wall'),
 (40,'en','App\Entity\FieldRenovationType','typeName','2','Sol'),
 (41,'en','App\Entity\FieldGroup','labelText','14','BULE SKY'),
 (42,'en','App\Entity\Field','labelText','30','Would you like a blue sky in season?'),
 (43,'en','App\Entity\Retouch','title','0','BLUE SKY'),
 (44,'en','App\Entity\Retouch','description','0','<p>For outdoor images.</p><p><strong>Only</strong> the sky is retouched.</p><p><br></p><p>&nbsp; Weather in grisaille, rainy or insipid sky?</p><p>- We apply a blue sky of season.</p><p>- We adapt the chromium to reflections: windows, rivers, rivers, seas ...</p><p><br></p>'),
 (45,'en','App\Entity\Retouch','title','1','RELEVANCE 360°'),
 (46,'en','App\Entity\Retouch','description','1','<p>For indoor photos.</p><p>Virtual storage:</p><p>- We store personal belongings,</p><p>- We balance chromie &amp; light,</p><p>- We recover the external views.</p><p>- No correction of optical distortions.</p>'),
 (47,'en','App\Entity\Retouch','title','2','2D PHOTO-STAGING'),
 (48,'en','App\Entity\Retouch','description','2','<p>Empty / renovate</p><p>- We completely empty the room, We remove the curtains only when the external view and door frames are visible, to restore.</p><p>- We renovate floors, walls and ceilings, according to your requests, those of your customers or pre-selected samples.</p><p>- You can now project yourself into the current good, emptied of its content, with your future choices of color and material.</p>'),
 (49,'en','App\Entity\Retouch','title','3','BALANCING'),
 (50,'en','App\Entity\Retouch','description','3','<p>The content of your photo is good, but the rendering of the image unsatisfactory:</p><p>* We balance the chromium, the light.</p><p>* We correct the optical distortions.</p><p>* We recover (uncork) the external views.</p><p>* The content of the image is not retouched.</p>'),
 (51,'en','App\Entity\Retouch','title','9','CREEPUSCULE''S ATMOSPHERE'),
 (52,'en','App\Entity\Retouch','description','9','<p>Outdoor photos.</p><p>For refined atmospheres, or cozy end of the day.</p><p>- We apply a sunset sky,</p><p>- We adapt the chromium to reflections: windows, rivers, rivers, seas ...</p><p>- We turn on your outdoor lights,</p><p>- We highlight your pools.</p>'),
 (53,'en','App\Entity\Field','labelText','10',NULL),
 (54,'en','App\Entity\Retouch','title','10',NULL),
 (55,'en','App\Entity\Retouch','description','10',NULL),
 (56,'en','App\Entity\Field','labelText','1',NULL),
 (57,'en','App\Entity\Field','labelText','4',NULL),
 (58,'en','App\Entity\FieldChoices','choiceLabel','3','PX'),
 (59,'en','App\Entity\FieldChoices','choiceLabel','4','CM'),
 (60,'en','App\Entity\FieldGroup','labelText','3','Logo'),
 (61,'en','App\Entity\Retouch','title','11','BLUE SKY 360°'),
 (62,'en','App\Entity\Retouch','description','11','<p>Weather in grisaille, rainy or bleak sky?</p><p>- We apply a blue sky of season.</p><p>- We adapt the chromium to reflections: windows, rivers, rivers, seas ...</p><p>- We "dry" the terraces or sidewalks ...</p>'),
 (63,'en','App\Entity\Retouch','title','12','RELEVANCE « INTERIOR »'),
 (64,'en','App\Entity\Retouch','description','12','<p>For indoor photos.</p><p>Virtual storage and decluttering:</p><p>- We store personal belongings,</p><p>- We unclutter the interior layout,</p><p>- We balance chromie &amp; light,</p><p>- We correct the optical distortions,</p><p>- We recover the external views.</p><p>This service has its limits, it requires to be able to reconstitute the materials, the forms, the style, the design of the furniture ...</p>'),
 (65,'en','App\Entity\Retouch','title','13','RELEVANCE « EXTERIOR »'),
 (66,'en','App\Entity\Retouch','description','13','<p>For outdoor photos.&nbsp;<span style="font-size: 0.9375rem;">Virtual storage and decluttering :</span></p><p>- We store personal belongings.</p><p>- We unclutter the exterior layout.</p><p>- We balance chromie &amp; light.</p><p>- We correct the optical distortions.</p>'),
 (67,'en','App\Entity\Retouch','title','14','PHOTO STAGING 2D'),
 (68,'en','App\Entity\Retouch','description','14','<p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;">Empty / renovate</span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;"><br></span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;">- We completely empty the room, We remove the curtains only when the external view and door frames are visible, to restore.</span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;"><br></span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;">- We renovate floors, walls and ceilings, according to your requests, those of your customers or pre-selected samples.</span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;"><br></span></font></p><p style="text-align: right;"><font color="#4272d7"><span style="font-size: 14px;">You can now project yourself into the current good, emptied of its content, with your future choices of color and material.</span></font></p>'),
 (69,'en','App\Entity\Retouch','title','15','BALANCING'),
 (70,'en','App\Entity\Retouch','description','15','<p style="color: rgb(66, 114, 215); font-size: 14px; text-align: right;">The content of your photo is good, but the rendering of the image unsatisfactory:</p><p style="color: rgb(66, 114, 215); font-size: 14px; text-align: right;">* We balance the chromium, the light.</p><p style="color: rgb(66, 114, 215); font-size: 14px; text-align: right;">* We correct the optical distortions.</p><p style="color: rgb(66, 114, 215); font-size: 14px; text-align: right;">* We recover (uncork) the external views.</p><p style="color: rgb(66, 114, 215); font-size: 14px; text-align: right;">* The content of the image is not retouched.</p>'),
 (71,'en','App\Entity\FieldGroup','labelText','11','For Virtual Photo-Staging, need to write:'),
 (72,'en','App\Entity\Field','labelText','22',NULL),
 (73,'en','App\Entity\Field','labelText','23',NULL),
 (74,'en','App\Entity\FieldGroup','labelText','16','Bed not done'),
 (75,'en','App\Entity\Field','labelText','32',NULL),
 (76,'en','App\Entity\FieldGroup','labelText','9','For OUTDOOR DEVELOPMENT, need to write:'),
 (77,'en','App\Entity\Field','labelText','24',NULL),
 (78,'en','App\Entity\Field','labelText','25',NULL),
 (79,'en','App\Entity\FieldGroup','labelText','12','Outdoor pool'),
 (80,'en','App\Entity\Field','labelText','28','Do you want to brighten your pool?'),
 (81,'en','App\Entity\FieldGroup','labelText','10','Specifications'),
 (82,'en','App\Entity\Field','labelText','26','Briefly describe your planning guidelines, 1 line per need.');
COMMIT;
