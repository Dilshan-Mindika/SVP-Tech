<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adapt user table
        Schema::table('user', function (Blueprint $table) {
            if (!Schema::hasColumn('user', 'remember_token')) {
                $table->string('remember_token', 100)->nullable();
            }
            if (!Schema::hasColumn('user', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('user', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('user', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('user', 'type')) {
                $table->enum('type', ['normal', 'shop'])->default('normal');
            }
            if (!Schema::hasColumn('user', 'credit_balance')) {
                $table->decimal('credit_balance', 10, 2)->default(0.00);
            }
        });

        // Alter role column in user table to support CUSTOMER_SUPPORT
        DB::statement("ALTER TABLE `user` MODIFY COLUMN `role` ENUM('USER', 'ADMIN', 'TECHNICIAN', 'CUSTOMER_SUPPORT') NOT NULL DEFAULT 'USER'");

        // 2. Adapt product table
        Schema::table('product', function (Blueprint $table) {
            if (!Schema::hasColumn('product', 'brand')) {
                $table->string('brand')->nullable();
            }
            if (!Schema::hasColumn('product', 'model')) {
                $table->string('model')->nullable();
            }
            if (!Schema::hasColumn('product', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(5);
            }
            if (!Schema::hasColumn('product', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('product', 'stock_quantity')) {
                $table->integer('stock_quantity')->nullable();
            }
            if (!Schema::hasColumn('product', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('product', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Initialize stock_quantity and selling_price if null
        DB::statement("UPDATE `product` SET `stock_quantity` = `stock` WHERE `stock_quantity` IS NULL");
        DB::statement("UPDATE `product` SET `selling_price` = `price` WHERE `selling_price` IS NULL");

        // 3. Adapt repair table
        Schema::table('repair', function (Blueprint $table) {
            if (!Schema::hasColumn('repair', 'job_number')) {
                $table->string('job_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('repair', 'job_type')) {
                $table->enum('job_type', ['repair', 'sale'])->default('repair');
            }
            if (!Schema::hasColumn('repair', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            }
            if (!Schema::hasColumn('repair', 'repair_status')) {
                $table->enum('repair_status', ['pending', 'in_progress', 'waiting_for_parts', 'completed', 'delivered', 'cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('repair', 'laptop_brand')) {
                $table->string('laptop_brand')->nullable();
            }
            if (!Schema::hasColumn('repair', 'laptop_model')) {
                $table->string('laptop_model')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_specs')) {
                $table->json('device_specs')->nullable();
            }
            if (!Schema::hasColumn('repair', 'invoice_generated')) {
                $table->boolean('invoice_generated')->default(false);
            }
            if (!Schema::hasColumn('repair', 'parts_used_cost')) {
                $table->decimal('parts_used_cost', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'labor_cost')) {
                $table->decimal('labor_cost', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'final_price')) {
                $table->decimal('final_price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'job_invoice_generated_at')) {
                $table->timestamp('job_invoice_generated_at')->nullable();
            }
            if (!Schema::hasColumn('repair', 'service_invoice_generated_at')) {
                $table->timestamp('service_invoice_generated_at')->nullable();
            }
            if (!Schema::hasColumn('repair', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('repair', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
            if (!Schema::hasColumn('repair', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('repair', 'customer_id')) {
                $table->integer('customer_id')->nullable();
            }
            if (!Schema::hasColumn('repair', 'technician_id')) {
                $table->integer('technician_id')->nullable();
            }
            if (!Schema::hasColumn('repair', 'fault_description')) {
                $table->text('fault_description')->nullable();
            }
            if (!Schema::hasColumn('repair', 'repair_notes')) {
                $table->text('repair_notes')->nullable();
            }
            if (!Schema::hasColumn('repair', 'serial_number')) {
                $table->string('serial_number')->nullable();
            }
        });

        // Initialize repair columns from existing data
        DB::statement("UPDATE `repair` SET `customer_id` = `userId` WHERE `customer_id` IS NULL");
        DB::statement("UPDATE `repair` SET `technician_id` = `technicianId` WHERE `technician_id` IS NULL");
        DB::statement("UPDATE `repair` SET `fault_description` = `issue` WHERE `fault_description` IS NULL");
        DB::statement("UPDATE `repair` SET `repair_notes` = `notes` WHERE `repair_notes` IS NULL");
        DB::statement("UPDATE `repair` SET `final_price` = `cost` WHERE `final_price` IS NULL");
        DB::statement("UPDATE `repair` SET `completed_at` = `completionDate` WHERE `completed_at` IS NULL");
        DB::statement("UPDATE `repair` SET `serial_number` = `serialNumber` WHERE `serial_number` IS NULL");
        DB::statement("UPDATE `repair` SET `laptop_brand` = `device`, `laptop_model` = '' WHERE `laptop_brand` IS NULL");
        DB::statement("UPDATE `repair` SET `job_number` = CONCAT('PWCRJ', LPAD(id, 6, '0')) WHERE `job_number` IS NULL");
        DB::statement("UPDATE `repair` SET `repair_status` = LOWER(`status`) WHERE `repair_status` IS NULL");

        // Alter status column in repair table to support DELIVERED and CANCELLED
        DB::statement("ALTER TABLE `repair` MODIFY COLUMN `status` ENUM('PENDING', 'RECEIVED', 'IN_PROGRESS', 'WAITING_FOR_PARTS', 'COMPLETED', 'READY_FOR_PICKUP', 'DELIVERED', 'CANCELLED') NOT NULL DEFAULT 'PENDING'");

        // 4. Create Sync Triggers
        
        // Trigger 4.1: product_before_insert
        DB::unprepared("
            DROP TRIGGER IF EXISTS `product_before_insert`;
            CREATE TRIGGER `product_before_insert` BEFORE INSERT ON `product`
            FOR EACH ROW
            BEGIN
                IF NEW.stock_quantity IS NULL THEN
                    SET NEW.stock_quantity = NEW.stock;
                ELSE
                    SET NEW.stock = NEW.stock_quantity;
                END IF;
                IF NEW.selling_price IS NULL THEN
                    SET NEW.selling_price = NEW.price;
                ELSE
                    SET NEW.price = NEW.selling_price;
                END IF;
            END
        ");

        // Trigger 4.2: product_before_update
        DB::unprepared("
            DROP TRIGGER IF EXISTS `product_before_update`;
            CREATE TRIGGER `product_before_update` BEFORE UPDATE ON `product`
            FOR EACH ROW
            BEGIN
                IF NOT (NEW.stock_quantity <=> OLD.stock_quantity) THEN
                    SET NEW.stock = NEW.stock_quantity;
                ELSEIF NOT (NEW.stock <=> OLD.stock) THEN
                    SET NEW.stock_quantity = NEW.stock;
                END IF;

                IF NOT (NEW.selling_price <=> OLD.selling_price) THEN
                    SET NEW.price = NEW.selling_price;
                ELSEIF NOT (NEW.price <=> OLD.price) THEN
                    SET NEW.selling_price = NEW.price;
                END IF;
            END
        ");

        // Trigger 4.3: repair_before_insert
        DB::unprepared("
            DROP TRIGGER IF EXISTS `repair_before_insert`;
            CREATE TRIGGER `repair_before_insert` BEFORE INSERT ON `repair`
            FOR EACH ROW
            BEGIN
                -- Sync customer_id and userId
                IF NEW.customer_id IS NULL THEN
                    SET NEW.customer_id = NEW.userId;
                ELSE
                    SET NEW.userId = NEW.customer_id;
                END IF;

                -- Sync technician_id and technicianId
                IF NEW.technician_id IS NULL THEN
                    SET NEW.technician_id = NEW.technicianId;
                ELSE
                    SET NEW.technicianId = NEW.technician_id;
                END IF;

                -- Sync device brand/model and device
                IF NEW.laptop_brand IS NULL AND NEW.laptop_model IS NULL THEN
                    SET NEW.laptop_brand = COALESCE(NEW.device, '');
                    SET NEW.laptop_model = '';
                ELSE
                    SET NEW.device = CONCAT(COALESCE(NEW.laptop_brand, ''), ' ', COALESCE(NEW.laptop_model, ''));
                END IF;

                -- Sync issue and fault_description
                IF NEW.fault_description IS NULL THEN
                    SET NEW.fault_description = NEW.issue;
                ELSE
                    SET NEW.issue = NEW.fault_description;
                END IF;

                -- Sync notes and repair_notes
                IF NEW.repair_notes IS NULL THEN
                    SET NEW.repair_notes = NEW.notes;
                ELSE
                    SET NEW.notes = NEW.repair_notes;
                END IF;

                -- Sync cost and final_price
                IF NEW.final_price IS NULL THEN
                    SET NEW.final_price = NEW.cost;
                ELSE
                    SET NEW.cost = NEW.final_price;
                END IF;

                -- Sync completionDate and completed_at
                IF NEW.completed_at IS NULL THEN
                    SET NEW.completed_at = NEW.completionDate;
                ELSE
                    SET NEW.completionDate = NEW.completed_at;
                END IF;

                -- Sync serialNumber and serial_number
                IF NEW.serial_number IS NULL THEN
                    SET NEW.serial_number = NEW.serialNumber;
                ELSE
                    SET NEW.serialNumber = NEW.serial_number;
                END IF;

                -- Sync status and repair_status
                IF NEW.repair_status IS NULL THEN
                    SET NEW.repair_status = LOWER(NEW.status);
                ELSE
                    SET NEW.status = UPPER(NEW.repair_status);
                END IF;

                -- Generate job_number if null
                IF NEW.job_number IS NULL THEN
                    SELECT Auto_increment INTO @next_id FROM information_schema.tables WHERE table_name='repair' AND table_schema=DATABASE();
                    SET NEW.job_number = CONCAT('PWCRJ', LPAD(COALESCE(@next_id, 1), 6, '0'));
                END IF;
            END
        ");

        // Trigger 4.4: repair_before_update
        DB::unprepared("
            DROP TRIGGER IF EXISTS `repair_before_update`;
            CREATE TRIGGER `repair_before_update` BEFORE UPDATE ON `repair`
            FOR EACH ROW
            BEGIN
                -- Sync customer_id and userId
                IF NOT (NEW.customer_id <=> OLD.customer_id) THEN
                    SET NEW.userId = NEW.customer_id;
                ELSEIF NOT (NEW.userId <=> OLD.userId) THEN
                    SET NEW.customer_id = NEW.userId;
                END IF;

                -- Sync technician_id and technicianId
                IF NOT (NEW.technician_id <=> OLD.technician_id) THEN
                    SET NEW.technicianId = NEW.technician_id;
                ELSEIF NOT (NEW.technicianId <=> OLD.technicianId) THEN
                    SET NEW.technician_id = NEW.technicianId;
                END IF;

                -- Sync device brand/model and device
                IF NOT (NEW.laptop_brand <=> OLD.laptop_brand) OR NOT (NEW.laptop_model <=> OLD.laptop_model) THEN
                    SET NEW.device = CONCAT(COALESCE(NEW.laptop_brand, ''), ' ', COALESCE(NEW.laptop_model, ''));
                ELSEIF NOT (NEW.device <=> OLD.device) THEN
                    SET NEW.laptop_brand = NEW.device;
                    SET NEW.laptop_model = '';
                END IF;

                -- Sync issue and fault_description
                IF NOT (NEW.fault_description <=> OLD.fault_description) THEN
                    SET NEW.issue = NEW.fault_description;
                ELSEIF NOT (NEW.issue <=> OLD.issue) THEN
                    SET NEW.fault_description = NEW.issue;
                END IF;

                -- Sync notes and repair_notes
                IF NOT (NEW.repair_notes <=> OLD.repair_notes) THEN
                    SET NEW.notes = NEW.repair_notes;
                ELSEIF NOT (NEW.notes <=> OLD.notes) THEN
                    SET NEW.repair_notes = NEW.notes;
                END IF;

                -- Sync cost and final_price
                IF NOT (NEW.final_price <=> OLD.final_price) THEN
                    SET NEW.cost = NEW.final_price;
                ELSEIF NOT (NEW.cost <=> OLD.cost) THEN
                    SET NEW.final_price = NEW.cost;
                END IF;

                -- Sync completionDate and completed_at
                IF NOT (NEW.completed_at <=> OLD.completed_at) THEN
                    SET NEW.completionDate = NEW.completed_at;
                ELSEIF NOT (NEW.completionDate <=> OLD.completionDate) THEN
                    SET NEW.completed_at = NEW.completionDate;
                END IF;

                -- Sync serialNumber and serial_number
                IF NOT (NEW.serial_number <=> OLD.serial_number) THEN
                    SET NEW.serialNumber = NEW.serial_number;
                ELSEIF NOT (NEW.serialNumber <=> OLD.serialNumber) THEN
                    SET NEW.serial_number = NEW.serialNumber;
                END IF;

                -- Sync status and repair_status
                IF NOT (NEW.repair_status <=> OLD.repair_status) THEN
                    SET NEW.status = UPPER(NEW.repair_status);
                ELSEIF NOT (NEW.status <=> OLD.status) THEN
                    SET NEW.repair_status = LOWER(NEW.status);
                END IF;
            END
        ");
    }

    public function down(): void
    {
        // Drop Triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_update`");

        // Roll back column adaptations
        Schema::table('repair', function (Blueprint $table) {
            $table->dropColumn([
                'job_number', 'job_type', 'payment_status', 'repair_status', 'laptop_brand', 'laptop_model',
                'device_specs', 'invoice_generated', 'parts_used_cost', 'labor_cost', 'final_price',
                'job_invoice_generated_at', 'service_invoice_generated_at', 'completed_at',
                'delivered_at', 'customer_id', 'technician_id', 'fault_description',
                'repair_notes', 'serial_number'
            ]);
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn([
                'brand', 'model', 'low_stock_threshold', 'cost_price', 'stock_quantity', 'selling_price', 'deleted_at'
            ]);
        });

        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn([
                'remember_token', 'email_verified_at', 'deleted_at', 'address', 'type', 'credit_balance'
            ]);
        });
    }
};
