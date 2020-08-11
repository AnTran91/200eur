<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190110131635 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
	    if ($this->connection->getDatabasePlatform()->getName() !== 'mysql'){
		    print "'mysql' Migration ignored .";
		    return;
	    }
	    
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, network_id INT DEFAULT NULL, registration_code VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C1EE637CB82B2744 (registration_code), INDEX IDX_C1EE637C7E3C61F9 (owner_id), INDEX IDX_C1EE637C34128B91 (network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', image_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(200) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, email_secondary VARCHAR(255) DEFAULT NULL, register_code VARCHAR(200) DEFAULT NULL, language VARCHAR(200) DEFAULT NULL, user_directory LONGTEXT DEFAULT NULL, api_token LONGTEXT DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, google_access_token VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME DEFAULT NULL, receive_newsletter TINYINT(1) DEFAULT NULL, receive_targeted_emails_from_promotion TINYINT(1) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, billing_address_first_name VARCHAR(70) DEFAULT NULL, billing_address_last_name VARCHAR(100) DEFAULT NULL, billing_address_address VARCHAR(255) DEFAULT NULL, billing_address_country VARCHAR(100) DEFAULT NULL, billing_address_city VARCHAR(150) DEFAULT NULL, billing_address_zip_code VARCHAR(20) DEFAULT NULL, billing_address_phone VARCHAR(50) DEFAULT NULL, billing_address_company VARCHAR(200) DEFAULT NULL, billing_address_network_name VARCHAR(255) DEFAULT NULL, billing_address_secondary_address VARCHAR(255) DEFAULT NULL, billing_address_corporate_name VARCHAR(255) DEFAULT NULL, billing_address_tva VARCHAR(200) DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), INDEX IDX_957A647932C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_delivery_time (id INT AUTO_INCREMENT NOT NULL, time INT DEFAULT NULL, unit VARCHAR(200) DEFAULT NULL, order_delivery_code VARCHAR(200) DEFAULT NULL, global TINYINT(1) NOT NULL, selected_by_default TINYINT(1) NOT NULL, app_type VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_group (id INT AUTO_INCREMENT NOT NULL, label_text VARCHAR(255) NOT NULL, position VARCHAR(50) NOT NULL, order_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_group_retouch (field_group_id INT NOT NULL, retouch_id INT NOT NULL, INDEX IDX_94277A2F286C5E8A (field_group_id), INDEX IDX_94277A2FB0C9087F (retouch_id), PRIMARY KEY(field_group_id, retouch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_4B019DDB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holidays (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, client_id INT DEFAULT NULL, invoice_number NUMERIC(10, 0) DEFAULT NULL, pdf_file_name VARCHAR(200) DEFAULT NULL, total_amount_paid NUMERIC(10, 2) DEFAULT NULL, total_amount NUMERIC(10, 2) DEFAULT NULL, tax_percentage NUMERIC(5, 2) DEFAULT NULL, reduction_percentage NUMERIC(5, 2) DEFAULT NULL, total_reduction_on_pictures NUMERIC(3, 0) DEFAULT NULL, total_reduction_amount NUMERIC(10, 2) DEFAULT NULL, creation_date DATETIME DEFAULT NULL, payment_date DATETIME DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, app_type VARCHAR(150) DEFAULT NULL, INDEX IDX_9065174432C8A3DE (organization_id), INDEX IDX_9065174419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emmo_order (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, affected_to_id INT DEFAULT NULL, id_promo INT DEFAULT NULL, production_id INT DEFAULT NULL, delivery_time_id INT DEFAULT NULL, order_number INT DEFAULT NULL, order_status VARCHAR(50) NOT NULL, send_email TINYINT(1) NOT NULL, upload_folder LONGTEXT DEFAULT NULL, total_amount NUMERIC(10, 2) NOT NULL, tax_percentage NUMERIC(4, 2) DEFAULT NULL, reduction_percentage NUMERIC(5, 2) DEFAULT NULL, total_reduction_on_pictures NUMERIC(3, 0) DEFAULT NULL, total_reduction_amount NUMERIC(10, 2) DEFAULT NULL, creation_date DATETIME NOT NULL, deliverance_date DATE DEFAULT NULL, payment_date DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, app_type VARCHAR(150) DEFAULT NULL, INDEX IDX_81AB967019EB6921 (client_id), INDEX IDX_81AB9670F3310D50 (affected_to_id), INDEX IDX_81AB96705E96DBCB (id_promo), INDEX IDX_81AB9670ECC6147F (production_id), INDEX IDX_81AB967054F462E5 (delivery_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_invoice (order_id INT NOT NULL, invoice_id INT NOT NULL, INDEX IDX_661FBE0F8D9F6D38 (order_id), INDEX IDX_661FBE0F2989F1FD (invoice_id), PRIMARY KEY(order_id, invoice_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_details (id INT AUTO_INCREMENT NOT NULL, retouch_id INT NOT NULL, picture_id INT DEFAULT NULL, param_id INT DEFAULT NULL, returned_picture_id INT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_6F00FF58B0C9087F (retouch_id), INDEX IDX_6F00FF58EE45BDBF (picture_id), UNIQUE INDEX UNIQ_6F00FF585647C863 (param_id), UNIQUE INDEX UNIQ_6F00FF58505D3329 (returned_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retouch (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) DEFAULT NULL, description LONGTEXT DEFAULT NULL, order_number INT DEFAULT NULL, retouch_code VARCHAR(200) DEFAULT NULL, app_type VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, promo_code VARCHAR(150) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, expired TINYINT(1) DEFAULT NULL, promo_type VARCHAR(150) NOT NULL, discr VARCHAR(255) NOT NULL, has_number_of_use TINYINT(1) DEFAULT NULL, use_limit NUMERIC(5, 0) DEFAULT NULL, use_limit_per_user NUMERIC(5, 0) DEFAULT NULL, INDEX IDX_B0139AFB32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo_user (promo_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C70A754FD0C07AFF (promo_id), INDEX IDX_C70A754FA76ED395 (user_id), PRIMARY KEY(promo_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emmo_transaction (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, wallet_id INT DEFAULT NULL, order_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, transaction_number INT DEFAULT NULL, transaction_ext_number LONGTEXT DEFAULT NULL, status_code VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, log_response LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', updated_at DATETIME DEFAULT NULL, paid TINYINT(1) DEFAULT NULL, card_number VARCHAR(50) DEFAULT NULL, card_brand VARCHAR(50) DEFAULT NULL, INDEX IDX_5468AD7219EB6921 (client_id), INDEX IDX_5468AD72712520F3 (wallet_id), UNIQUE INDEX UNIQ_5468AD728D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, picture_name VARCHAR(255) DEFAULT NULL, picture_path LONGTEXT DEFAULT NULL, painted_picture_path LONGTEXT DEFAULT NULL, painted_picture_path_thumb LONGTEXT DEFAULT NULL, picture_path_thumb LONGTEXT DEFAULT NULL, picture_directory VARCHAR(150) DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, commentary LONGTEXT DEFAULT NULL, INDEX IDX_16DB4F898D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, current_amount NUMERIC(10, 2) NOT NULL, last_update DATETIME NOT NULL, UNIQUE INDEX UNIQ_7C68921F19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_renovation_choices (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, picture_path LONGTEXT DEFAULT NULL, uuid VARCHAR(255) DEFAULT NULL, INDEX IDX_5F23A48EC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, disabled_on_id INT DEFAULT NULL, field_group_id INT DEFAULT NULL, name VARCHAR(200) NOT NULL, label_text VARCHAR(255) DEFAULT NULL, empty_data VARCHAR(255) DEFAULT NULL, mapped TINYINT(1) DEFAULT NULL, price NUMERIC(5, 2) DEFAULT NULL, add_the_price_when_value_equals_to VARCHAR(200) DEFAULT NULL, field_type VARCHAR(200) NOT NULL, order_number INT NOT NULL, htmlclass VARCHAR(150) DEFAULT NULL, disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_5BF545585E237E06 (name), INDEX IDX_5BF5455823D615C7 (disabled_on_id), INDEX IDX_5BF54558286C5E8A (field_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_field_choices (field_id INT NOT NULL, field_choices_id INT NOT NULL, INDEX IDX_77FBC7C0443707B0 (field_id), INDEX IDX_77FBC7C01BB4B0B9 (field_choices_id), PRIMARY KEY(field_id, field_choices_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_field_renovation_type (field_id INT NOT NULL, field_renovation_type_id INT NOT NULL, INDEX IDX_7F073864443707B0 (field_id), INDEX IDX_7F0738649FA1BE24 (field_renovation_type_id), PRIMARY KEY(field_id, field_renovation_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_renovation_type (id INT AUTO_INCREMENT NOT NULL, type_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_choices (id INT AUTO_INCREMENT NOT NULL, choice_label VARCHAR(255) NOT NULL, choice_value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_counter_per_retouch (id INT AUTO_INCREMENT NOT NULL, picture_counter_id INT DEFAULT NULL, retouch_id INT DEFAULT NULL, image_counter_limit NUMERIC(5, 0) NOT NULL, image_counter_limit_with_reduction NUMERIC(5, 0) NOT NULL, image_counter_reduction NUMERIC(5, 2) NOT NULL, INDEX IDX_8412C9FED03EB2E4 (picture_counter_id), INDEX IDX_8412C9FEB0C9087F (retouch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE param_collection (id INT AUTO_INCREMENT NOT NULL, elements LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_discount_per_retouch (id INT AUTO_INCREMENT NOT NULL, retouch_id INT DEFAULT NULL, picture_discount_id INT DEFAULT NULL, reduction NUMERIC(5, 2) NOT NULL, image_limit NUMERIC(5, 0) DEFAULT NULL, image_limit_per_order NUMERIC(5, 0) DEFAULT NULL, image_limit_per_user NUMERIC(5, 0) DEFAULT NULL, INDEX IDX_935D6AA4B0C9087F (retouch_id), INDEX IDX_935D6AA4D23424FC (picture_discount_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field_details (id INT AUTO_INCREMENT NOT NULL, picture_detail_id INT DEFAULT NULL, field_id INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_9C022E2AD10E8F05 (picture_detail_id), INDEX IDX_9C022E2A443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_retouching_pricing (id INT AUTO_INCREMENT NOT NULL, retouch_id INT DEFAULT NULL, order_delivery_time_id INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_64945F65B0C9087F (retouch_id), INDEX IDX_64945F6536E276F9 (order_delivery_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C34128B91 FOREIGN KEY (network_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_group (id)');
        $this->addSql('ALTER TABLE field_group_retouch ADD CONSTRAINT FK_94277A2F286C5E8A FOREIGN KEY (field_group_id) REFERENCES field_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_group_retouch ADD CONSTRAINT FK_94277A2FB0C9087F FOREIGN KEY (retouch_id) REFERENCES retouch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE emmo_order ADD CONSTRAINT FK_81AB967019EB6921 FOREIGN KEY (client_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE emmo_order ADD CONSTRAINT FK_81AB9670F3310D50 FOREIGN KEY (affected_to_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE emmo_order ADD CONSTRAINT FK_81AB96705E96DBCB FOREIGN KEY (id_promo) REFERENCES promo (id)');
        $this->addSql('ALTER TABLE emmo_order ADD CONSTRAINT FK_81AB9670ECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE emmo_order ADD CONSTRAINT FK_81AB967054F462E5 FOREIGN KEY (delivery_time_id) REFERENCES order_delivery_time (id)');
        $this->addSql('ALTER TABLE order_invoice ADD CONSTRAINT FK_661FBE0F8D9F6D38 FOREIGN KEY (order_id) REFERENCES emmo_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_invoice ADD CONSTRAINT FK_661FBE0F2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture_details ADD CONSTRAINT FK_6F00FF58B0C9087F FOREIGN KEY (retouch_id) REFERENCES retouch (id)');
        $this->addSql('ALTER TABLE picture_details ADD CONSTRAINT FK_6F00FF58EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE picture_details ADD CONSTRAINT FK_6F00FF585647C863 FOREIGN KEY (param_id) REFERENCES param_collection (id)');
        $this->addSql('ALTER TABLE picture_details ADD CONSTRAINT FK_6F00FF58505D3329 FOREIGN KEY (returned_picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE promo ADD CONSTRAINT FK_B0139AFB32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE promo_user ADD CONSTRAINT FK_C70A754FD0C07AFF FOREIGN KEY (promo_id) REFERENCES promo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promo_user ADD CONSTRAINT FK_C70A754FA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE emmo_transaction ADD CONSTRAINT FK_5468AD7219EB6921 FOREIGN KEY (client_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE emmo_transaction ADD CONSTRAINT FK_5468AD72712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE emmo_transaction ADD CONSTRAINT FK_5468AD728D9F6D38 FOREIGN KEY (order_id) REFERENCES emmo_order (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F898D9F6D38 FOREIGN KEY (order_id) REFERENCES emmo_order (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F19EB6921 FOREIGN KEY (client_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE field_renovation_choices ADD CONSTRAINT FK_5F23A48EC54C8C93 FOREIGN KEY (type_id) REFERENCES field_renovation_type (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455823D615C7 FOREIGN KEY (disabled_on_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558286C5E8A FOREIGN KEY (field_group_id) REFERENCES field_group (id)');
        $this->addSql('ALTER TABLE field_field_choices ADD CONSTRAINT FK_77FBC7C0443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_field_choices ADD CONSTRAINT FK_77FBC7C01BB4B0B9 FOREIGN KEY (field_choices_id) REFERENCES field_choices (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_field_renovation_type ADD CONSTRAINT FK_7F073864443707B0 FOREIGN KEY (field_id) REFERENCES field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_field_renovation_type ADD CONSTRAINT FK_7F0738649FA1BE24 FOREIGN KEY (field_renovation_type_id) REFERENCES field_renovation_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture_counter_per_retouch ADD CONSTRAINT FK_8412C9FED03EB2E4 FOREIGN KEY (picture_counter_id) REFERENCES promo (id)');
        $this->addSql('ALTER TABLE picture_counter_per_retouch ADD CONSTRAINT FK_8412C9FEB0C9087F FOREIGN KEY (retouch_id) REFERENCES retouch (id)');
        $this->addSql('ALTER TABLE picture_discount_per_retouch ADD CONSTRAINT FK_935D6AA4B0C9087F FOREIGN KEY (retouch_id) REFERENCES retouch (id)');
        $this->addSql('ALTER TABLE picture_discount_per_retouch ADD CONSTRAINT FK_935D6AA4D23424FC FOREIGN KEY (picture_discount_id) REFERENCES promo (id)');
        $this->addSql('ALTER TABLE field_details ADD CONSTRAINT FK_9C022E2AD10E8F05 FOREIGN KEY (picture_detail_id) REFERENCES picture_details (id)');
        $this->addSql('ALTER TABLE field_details ADD CONSTRAINT FK_9C022E2A443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE photo_retouching_pricing ADD CONSTRAINT FK_64945F65B0C9087F FOREIGN KEY (retouch_id) REFERENCES retouch (id)');
        $this->addSql('ALTER TABLE photo_retouching_pricing ADD CONSTRAINT FK_64945F6536E276F9 FOREIGN KEY (order_delivery_time_id) REFERENCES order_delivery_time (id)');
    }

    public function down(Schema $schema) : void
    {
	    if ($this->connection->getDatabasePlatform()->getName() !== 'mysql'){
		    print "'mysql' Migration ignored .";
		    return;
	    }
	    
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C34128B91');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647932C8A3DE');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174432C8A3DE');
        $this->addSql('ALTER TABLE promo DROP FOREIGN KEY FK_B0139AFB32C8A3DE');
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C7E3C61F9');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174419EB6921');
        $this->addSql('ALTER TABLE emmo_order DROP FOREIGN KEY FK_81AB967019EB6921');
        $this->addSql('ALTER TABLE emmo_order DROP FOREIGN KEY FK_81AB9670F3310D50');
        $this->addSql('ALTER TABLE promo_user DROP FOREIGN KEY FK_C70A754FA76ED395');
        $this->addSql('ALTER TABLE emmo_transaction DROP FOREIGN KEY FK_5468AD7219EB6921');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F19EB6921');
        $this->addSql('ALTER TABLE emmo_order DROP FOREIGN KEY FK_81AB967054F462E5');
        $this->addSql('ALTER TABLE photo_retouching_pricing DROP FOREIGN KEY FK_64945F6536E276F9');
        $this->addSql('ALTER TABLE field_group_retouch DROP FOREIGN KEY FK_94277A2F286C5E8A');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558286C5E8A');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE order_invoice DROP FOREIGN KEY FK_661FBE0F2989F1FD');
        $this->addSql('ALTER TABLE order_invoice DROP FOREIGN KEY FK_661FBE0F8D9F6D38');
        $this->addSql('ALTER TABLE emmo_transaction DROP FOREIGN KEY FK_5468AD728D9F6D38');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F898D9F6D38');
        $this->addSql('ALTER TABLE field_details DROP FOREIGN KEY FK_9C022E2AD10E8F05');
        $this->addSql('ALTER TABLE field_group_retouch DROP FOREIGN KEY FK_94277A2FB0C9087F');
        $this->addSql('ALTER TABLE picture_details DROP FOREIGN KEY FK_6F00FF58B0C9087F');
        $this->addSql('ALTER TABLE picture_counter_per_retouch DROP FOREIGN KEY FK_8412C9FEB0C9087F');
        $this->addSql('ALTER TABLE picture_discount_per_retouch DROP FOREIGN KEY FK_935D6AA4B0C9087F');
        $this->addSql('ALTER TABLE photo_retouching_pricing DROP FOREIGN KEY FK_64945F65B0C9087F');
        $this->addSql('ALTER TABLE emmo_order DROP FOREIGN KEY FK_81AB9670ECC6147F');
        $this->addSql('ALTER TABLE emmo_order DROP FOREIGN KEY FK_81AB96705E96DBCB');
        $this->addSql('ALTER TABLE promo_user DROP FOREIGN KEY FK_C70A754FD0C07AFF');
        $this->addSql('ALTER TABLE picture_counter_per_retouch DROP FOREIGN KEY FK_8412C9FED03EB2E4');
        $this->addSql('ALTER TABLE picture_discount_per_retouch DROP FOREIGN KEY FK_935D6AA4D23424FC');
        $this->addSql('ALTER TABLE picture_details DROP FOREIGN KEY FK_6F00FF58EE45BDBF');
        $this->addSql('ALTER TABLE picture_details DROP FOREIGN KEY FK_6F00FF58505D3329');
        $this->addSql('ALTER TABLE emmo_transaction DROP FOREIGN KEY FK_5468AD72712520F3');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455823D615C7');
        $this->addSql('ALTER TABLE field_field_choices DROP FOREIGN KEY FK_77FBC7C0443707B0');
        $this->addSql('ALTER TABLE field_field_renovation_type DROP FOREIGN KEY FK_7F073864443707B0');
        $this->addSql('ALTER TABLE field_details DROP FOREIGN KEY FK_9C022E2A443707B0');
        $this->addSql('ALTER TABLE field_renovation_choices DROP FOREIGN KEY FK_5F23A48EC54C8C93');
        $this->addSql('ALTER TABLE field_field_renovation_type DROP FOREIGN KEY FK_7F0738649FA1BE24');
        $this->addSql('ALTER TABLE field_field_choices DROP FOREIGN KEY FK_77FBC7C01BB4B0B9');
        $this->addSql('ALTER TABLE picture_details DROP FOREIGN KEY FK_6F00FF585647C863');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE order_delivery_time');
        $this->addSql('DROP TABLE field_group');
        $this->addSql('DROP TABLE field_group_retouch');
        $this->addSql('DROP TABLE fos_group');
        $this->addSql('DROP TABLE holidays');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE emmo_order');
        $this->addSql('DROP TABLE order_invoice');
        $this->addSql('DROP TABLE picture_details');
        $this->addSql('DROP TABLE retouch');
        $this->addSql('DROP TABLE production');
        $this->addSql('DROP TABLE promo');
        $this->addSql('DROP TABLE promo_user');
        $this->addSql('DROP TABLE emmo_transaction');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE field_renovation_choices');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE field_field_choices');
        $this->addSql('DROP TABLE field_field_renovation_type');
        $this->addSql('DROP TABLE field_renovation_type');
        $this->addSql('DROP TABLE field_choices');
        $this->addSql('DROP TABLE picture_counter_per_retouch');
        $this->addSql('DROP TABLE param_collection');
        $this->addSql('DROP TABLE picture_discount_per_retouch');
        $this->addSql('DROP TABLE field_details');
        $this->addSql('DROP TABLE photo_retouching_pricing');
    }
}
