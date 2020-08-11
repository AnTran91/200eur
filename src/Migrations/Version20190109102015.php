<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109102015 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
	    if ($this->connection->getDatabasePlatform()->getName() !== 'mysql'){
		    print "'mysql' Migration ignored .";
		    return;
	    }
	    
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE ext_translations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
        $this->addSql('CREATE TABLE ext_log_entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INTEGER NOT NULL, data CLOB DEFAULT NULL --(DC2Type:array)
        , username VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('CREATE TABLE organization (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, network_id INTEGER DEFAULT NULL, registration_code VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1EE637CB82B2744 ON organization (registration_code)');
        $this->addSql('CREATE INDEX IDX_C1EE637C7E3C61F9 ON organization (owner_id)');
        $this->addSql('CREATE INDEX IDX_C1EE637C34128B91 ON organization (network_id)');
        $this->addSql('CREATE TABLE fos_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organization_id INTEGER DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , image_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(200) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, email_secondary VARCHAR(255) DEFAULT NULL, register_code VARCHAR(200) DEFAULT NULL, language VARCHAR(200) DEFAULT NULL, user_directory CLOB DEFAULT NULL, api_token CLOB DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, google_access_token VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME DEFAULT NULL, receive_newsletter BOOLEAN DEFAULT NULL, receive_targeted_emails_from_promotion BOOLEAN DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, billing_address_first_name VARCHAR(70) DEFAULT NULL, billing_address_last_name VARCHAR(100) DEFAULT NULL, billing_address_address VARCHAR(255) DEFAULT NULL, billing_address_country VARCHAR(100) DEFAULT NULL, billing_address_city VARCHAR(150) DEFAULT NULL, billing_address_zip_code VARCHAR(20) DEFAULT NULL, billing_address_phone VARCHAR(50) DEFAULT NULL, billing_address_company VARCHAR(200) DEFAULT NULL, billing_address_network_name VARCHAR(255) DEFAULT NULL, billing_address_secondary_address VARCHAR(255) DEFAULT NULL, billing_address_corporate_name VARCHAR(255) DEFAULT NULL, billing_address_tva VARCHAR(200) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479C05FB297 ON fos_user (confirmation_token)');
        $this->addSql('CREATE INDEX IDX_957A647932C8A3DE ON fos_user (organization_id)');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, PRIMARY KEY(user_id, group_id))');
        $this->addSql('CREATE INDEX IDX_B3C77447A76ED395 ON fos_user_user_group (user_id)');
        $this->addSql('CREATE INDEX IDX_B3C77447FE54D947 ON fos_user_user_group (group_id)');
        $this->addSql('CREATE TABLE order_delivery_time (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time INTEGER DEFAULT NULL, unit VARCHAR(200) DEFAULT NULL, order_delivery_code VARCHAR(200) DEFAULT NULL, global BOOLEAN NOT NULL, selected_by_default BOOLEAN NOT NULL, app_type VARCHAR(150) DEFAULT NULL)');
        $this->addSql('CREATE TABLE field_group (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label_text VARCHAR(255) NOT NULL, position VARCHAR(50) NOT NULL, order_number INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE field_group_retouch (field_group_id INTEGER NOT NULL, retouch_id INTEGER NOT NULL, PRIMARY KEY(field_group_id, retouch_id))');
        $this->addSql('CREATE INDEX IDX_94277A2F286C5E8A ON field_group_retouch (field_group_id)');
        $this->addSql('CREATE INDEX IDX_94277A2FB0C9087F ON field_group_retouch (retouch_id)');
        $this->addSql('CREATE TABLE fos_group (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:array)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B019DDB5E237E06 ON fos_group (name)');
        $this->addSql('CREATE TABLE holidays (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL)');
        $this->addSql('CREATE TABLE invoice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organization_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, invoice_number NUMERIC(10, 0) DEFAULT NULL, pdf_file_name VARCHAR(200) DEFAULT NULL, total_amount_paid NUMERIC(10, 2) DEFAULT NULL, total_amount NUMERIC(10, 2) DEFAULT NULL, tax_percentage NUMERIC(5, 2) DEFAULT NULL, reduction_percentage NUMERIC(5, 2) DEFAULT NULL, total_reduction_on_pictures NUMERIC(3, 0) DEFAULT NULL, total_reduction_amount NUMERIC(10, 2) DEFAULT NULL, creation_date DATETIME DEFAULT NULL, payment_date DATETIME DEFAULT NULL, type VARCHAR(150) DEFAULT NULL, app_type VARCHAR(150) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_9065174432C8A3DE ON invoice (organization_id)');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('CREATE TABLE emmo_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, affected_to_id INTEGER DEFAULT NULL, id_promo INTEGER DEFAULT NULL, production_id INTEGER DEFAULT NULL, delivery_time_id INTEGER DEFAULT NULL, order_number INTEGER DEFAULT NULL, order_status VARCHAR(50) NOT NULL, send_email BOOLEAN NOT NULL, upload_folder CLOB DEFAULT NULL, total_amount NUMERIC(10, 2) NOT NULL, tax_percentage NUMERIC(4, 2) DEFAULT NULL, reduction_percentage NUMERIC(5, 2) DEFAULT NULL, total_reduction_on_pictures NUMERIC(3, 0) DEFAULT NULL, total_reduction_amount NUMERIC(10, 2) DEFAULT NULL, creation_date DATETIME NOT NULL, deliverance_date DATE DEFAULT NULL, payment_date DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, app_type VARCHAR(150) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_81AB967019EB6921 ON emmo_order (client_id)');
        $this->addSql('CREATE INDEX IDX_81AB9670F3310D50 ON emmo_order (affected_to_id)');
        $this->addSql('CREATE INDEX IDX_81AB96705E96DBCB ON emmo_order (id_promo)');
        $this->addSql('CREATE INDEX IDX_81AB9670ECC6147F ON emmo_order (production_id)');
        $this->addSql('CREATE INDEX IDX_81AB967054F462E5 ON emmo_order (delivery_time_id)');
        $this->addSql('CREATE TABLE order_invoice (order_id INTEGER NOT NULL, invoice_id INTEGER NOT NULL, PRIMARY KEY(order_id, invoice_id))');
        $this->addSql('CREATE INDEX IDX_661FBE0F8D9F6D38 ON order_invoice (order_id)');
        $this->addSql('CREATE INDEX IDX_661FBE0F2989F1FD ON order_invoice (invoice_id)');
        $this->addSql('CREATE TABLE picture_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, retouch_id INTEGER NOT NULL, picture_id INTEGER DEFAULT NULL, param_id INTEGER DEFAULT NULL, returned_picture_id INTEGER DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, price NUMERIC(10, 2) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_6F00FF58B0C9087F ON picture_details (retouch_id)');
        $this->addSql('CREATE INDEX IDX_6F00FF58EE45BDBF ON picture_details (picture_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F00FF585647C863 ON picture_details (param_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F00FF58505D3329 ON picture_details (returned_picture_id)');
        $this->addSql('CREATE TABLE retouch (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(200) DEFAULT NULL, description CLOB DEFAULT NULL, order_number INTEGER DEFAULT NULL, retouch_code VARCHAR(200) DEFAULT NULL, app_type VARCHAR(150) DEFAULT NULL)');
        $this->addSql('CREATE TABLE production (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, country VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE TABLE promo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, organization_id INTEGER DEFAULT NULL, promo_code VARCHAR(150) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, expired BOOLEAN DEFAULT NULL, promo_type VARCHAR(150) NOT NULL, discr VARCHAR(255) NOT NULL, has_number_of_use BOOLEAN DEFAULT NULL, use_limit NUMERIC(5, 0) DEFAULT NULL, use_limit_per_user NUMERIC(5, 0) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_B0139AFB32C8A3DE ON promo (organization_id)');
        $this->addSql('CREATE TABLE promo_user (promo_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(promo_id, user_id))');
        $this->addSql('CREATE INDEX IDX_C70A754FD0C07AFF ON promo_user (promo_id)');
        $this->addSql('CREATE INDEX IDX_C70A754FA76ED395 ON promo_user (user_id)');
        $this->addSql('CREATE TABLE emmo_transaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, wallet_id INTEGER DEFAULT NULL, order_id INTEGER DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, transaction_number INTEGER DEFAULT NULL, transaction_ext_number CLOB DEFAULT NULL, status_code VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, log_response CLOB DEFAULT NULL --(DC2Type:array)
        , updated_at DATETIME DEFAULT NULL, paid BOOLEAN DEFAULT NULL, card_number VARCHAR(50) DEFAULT NULL, card_brand VARCHAR(50) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_5468AD7219EB6921 ON emmo_transaction (client_id)');
        $this->addSql('CREATE INDEX IDX_5468AD72712520F3 ON emmo_transaction (wallet_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5468AD728D9F6D38 ON emmo_transaction (order_id)');
        $this->addSql('CREATE TABLE picture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, picture_name VARCHAR(255) DEFAULT NULL, picture_path CLOB DEFAULT NULL, painted_picture_path CLOB DEFAULT NULL, painted_picture_path_thumb CLOB DEFAULT NULL, picture_path_thumb CLOB DEFAULT NULL, picture_directory VARCHAR(150) DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, commentary CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_16DB4F898D9F6D38 ON picture (order_id)');
        $this->addSql('CREATE TABLE wallet (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, current_amount NUMERIC(10, 2) NOT NULL, last_update DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C68921F19EB6921 ON wallet (client_id)');
        $this->addSql('CREATE TABLE field_renovation_choices (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER DEFAULT NULL, picture_path CLOB DEFAULT NULL, uuid VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_5F23A48EC54C8C93 ON field_renovation_choices (type_id)');
        $this->addSql('CREATE TABLE field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, disabled_on_id INTEGER DEFAULT NULL, field_group_id INTEGER DEFAULT NULL, name VARCHAR(200) NOT NULL, label_text VARCHAR(255) DEFAULT NULL, empty_data VARCHAR(255) DEFAULT NULL, mapped BOOLEAN DEFAULT NULL, price NUMERIC(5, 2) DEFAULT NULL, add_the_price_when_value_equals_to VARCHAR(200) DEFAULT NULL, field_type VARCHAR(200) NOT NULL, order_number INTEGER NOT NULL, htmlclass VARCHAR(150) DEFAULT NULL, disabled BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5BF545585E237E06 ON field (name)');
        $this->addSql('CREATE INDEX IDX_5BF5455823D615C7 ON field (disabled_on_id)');
        $this->addSql('CREATE INDEX IDX_5BF54558286C5E8A ON field (field_group_id)');
        $this->addSql('CREATE TABLE field_field_choices (field_id INTEGER NOT NULL, field_choices_id INTEGER NOT NULL, PRIMARY KEY(field_id, field_choices_id))');
        $this->addSql('CREATE INDEX IDX_77FBC7C0443707B0 ON field_field_choices (field_id)');
        $this->addSql('CREATE INDEX IDX_77FBC7C01BB4B0B9 ON field_field_choices (field_choices_id)');
        $this->addSql('CREATE TABLE field_field_renovation_type (field_id INTEGER NOT NULL, field_renovation_type_id INTEGER NOT NULL, PRIMARY KEY(field_id, field_renovation_type_id))');
        $this->addSql('CREATE INDEX IDX_7F073864443707B0 ON field_field_renovation_type (field_id)');
        $this->addSql('CREATE INDEX IDX_7F0738649FA1BE24 ON field_field_renovation_type (field_renovation_type_id)');
        $this->addSql('CREATE TABLE field_renovation_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE field_choices (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, choice_label VARCHAR(255) NOT NULL, choice_value VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE picture_counter_per_retouch (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, picture_counter_id INTEGER DEFAULT NULL, retouch_id INTEGER DEFAULT NULL, image_counter_limit NUMERIC(5, 0) NOT NULL, image_counter_limit_with_reduction NUMERIC(5, 0) NOT NULL, image_counter_reduction NUMERIC(5, 2) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8412C9FED03EB2E4 ON picture_counter_per_retouch (picture_counter_id)');
        $this->addSql('CREATE INDEX IDX_8412C9FEB0C9087F ON picture_counter_per_retouch (retouch_id)');
        $this->addSql('CREATE TABLE param_collection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, elements CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('CREATE TABLE picture_discount_per_retouch (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, retouch_id INTEGER DEFAULT NULL, picture_discount_id INTEGER DEFAULT NULL, reduction NUMERIC(5, 2) NOT NULL, image_limit NUMERIC(5, 0) DEFAULT NULL, image_limit_per_order NUMERIC(5, 0) DEFAULT NULL, image_limit_per_user NUMERIC(5, 0) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_935D6AA4B0C9087F ON picture_discount_per_retouch (retouch_id)');
        $this->addSql('CREATE INDEX IDX_935D6AA4D23424FC ON picture_discount_per_retouch (picture_discount_id)');
        $this->addSql('CREATE TABLE field_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, picture_detail_id INTEGER DEFAULT NULL, field_id INTEGER DEFAULT NULL, price NUMERIC(10, 2) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9C022E2AD10E8F05 ON field_details (picture_detail_id)');
        $this->addSql('CREATE INDEX IDX_9C022E2A443707B0 ON field_details (field_id)');
        $this->addSql('CREATE TABLE photo_retouching_pricing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, retouch_id INTEGER DEFAULT NULL, order_delivery_time_id INTEGER DEFAULT NULL, price NUMERIC(10, 2) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_64945F65B0C9087F ON photo_retouching_pricing (retouch_id)');
        $this->addSql('CREATE INDEX IDX_64945F6536E276F9 ON photo_retouching_pricing (order_delivery_time_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

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
