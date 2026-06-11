<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Categories Table
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('parent_id')->unsigned()->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            });
        }

        // 2. Create Roles Table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create Permissions Table
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('module');
                $table->timestamps();
            });
        }

        // 4. Create Permission Role Pivot Table
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->bigInteger('role_id')->unsigned();
                $table->bigInteger('permission_id')->unsigned();
                $table->primary(['role_id', 'permission_id']);

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }

        // 5. Create Employees Table
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('name');
                $table->string('designation'); // Admin, Manager, Cashier, Technician
                $table->decimal('salary_amount', 10, 2);
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->date('joining_date');
                $table->string('status')->default('active'); // active, inactive
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
            });
        }

        // 6. Create Salaries Table
        if (!Schema::hasTable('salaries')) {
            Schema::create('salaries', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('employee_id')->unsigned();
                $table->decimal('amount_paid', 10, 2);
                $table->string('paid_for_month'); // e.g., "May 2026"
                $table->date('payment_date');
                $table->string('payment_method')->default('Bank Transfer'); // Cash, Bank Transfer, Check
                $table->string('payslip_no')->unique();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }

        // 7. Create GRNs Table
        if (!Schema::hasTable('grns')) {
            Schema::create('grns', function (Blueprint $table) {
                $table->id();
                $table->string('grn_number')->unique();
                $table->bigInteger('supplier_id')->unsigned();
                $table->integer('received_by');
                $table->date('date_received');
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('discount_percentage', 5, 2)->default(0.00);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('service_charges', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2);
                $table->string('payment_type')->default('Cash');
                $table->boolean('is_paid')->default(true);
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
                $table->foreign('received_by')->references('id')->on('user')->onDelete('cascade');
            });
        }

        // 8. Create GRN Items Table
        if (!Schema::hasTable('grn_items')) {
            Schema::create('grn_items', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('grn_id')->unsigned();
                $table->integer('product_id');
                $table->integer('quantity');
                $table->integer('free_quantity')->default(0);
                $table->decimal('buying_price', 10, 2);
                $table->decimal('wholesale_price', 10, 2)->default(0.00);
                $table->string('barcode')->nullable();
                $table->date('expire_date')->nullable();
                $table->integer('warranty_months')->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0.00);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('single_discount_amount', 10, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('grn_id')->references('id')->on('grns')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 9. Create Loyalty Transactions Table
        if (!Schema::hasTable('loyalty_transactions')) {
            Schema::create('loyalty_transactions', function (Blueprint $table) {
                $table->id();
                $table->integer('customer_id');
                $table->integer('points'); // signed, positive for earned, negative for redeemed
                $table->string('transaction_type'); // earned, redeemed
                $table->string('description');
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('user')->onDelete('cascade');
            });
        }

        // 10. Create Product Serials Table
        if (!Schema::hasTable('product_serials')) {
            Schema::create('product_serials', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->string('serial_number')->unique();
                $table->string('status')->default('in_stock'); // in_stock, sold, returned, under_repair
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 11. Create Quotations Table
        if (!Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->string('quotation_number')->unique();
                $table->integer('customer_id')->nullable();
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0.00);
                $table->decimal('total', 10, 2);
                $table->date('valid_until');
                $table->string('status')->default('draft'); // draft, sent, accepted, expired
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('user')->onDelete('set null');
            });
        }

        // 12. Create Quotation Items Table
        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('quotation_id')->unsigned();
                $table->integer('product_id');
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();

                $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 13. Create Invoice Items Table
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('invoice_id')->unsigned();
                $table->integer('product_id');
                $table->integer('quantity');
                $table->integer('free_quantity')->default(0);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('discount_percentage', 10, 2)->default(0.00);
                $table->decimal('total', 10, 2)->default(0.00);
                $table->string('serial_number')->nullable();
                $table->string('warranty')->nullable();
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 14. Create Warranty Claims Table
        if (!Schema::hasTable('warranty_claims')) {
            Schema::create('warranty_claims', function (Blueprint $table) {
                $table->id();
                $table->string('claim_number')->unique();
                $table->bigInteger('invoice_id')->nullable()->unsigned();
                $table->integer('product_id');
                $table->string('serial_number')->nullable();
                $table->integer('customer_id');
                $table->date('claim_date');
                $table->text('issue_description');
                $table->string('status')->default('pending'); // pending, sent_to_supplier, replaced, returned_to_customer, rejected
                $table->text('action_taken')->nullable();
                $table->date('closed_date')->nullable();
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
                $table->foreign('customer_id')->references('id')->on('user')->onDelete('cascade');
            });
        }

        // 15. Create Repair Items (utilized spare parts)
        if (!Schema::hasTable('repair_items')) {
            Schema::create('repair_items', function (Blueprint $table) {
                $table->id();
                $table->integer('repair_id');
                $table->integer('product_id');
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();

                $table->foreign('repair_id')->references('id')->on('repair')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 16. Create Returns Table
        if (!Schema::hasTable('returns')) {
            Schema::create('returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_number')->unique();
                $table->bigInteger('invoice_id')->nullable()->unsigned();
                $table->bigInteger('supplier_id')->nullable()->unsigned();
                $table->string('type'); // customer_return, supplier_return
                $table->text('reason');
                $table->decimal('refund_amount', 10, 2);
                $table->string('status')->default('completed'); // pending, completed
                $table->timestamps();

                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            });
        }

        // 17. Create Return Items Table
        if (!Schema::hasTable('return_items')) {
            Schema::create('return_items', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('return_id')->unsigned();
                $table->integer('product_id');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->timestamps();

                $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            });
        }

        // 18. Create Expenses Table
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->string('expense_no')->unique();
                $table->string('category'); // Rent, Utilities, Salary, Repair Parts, Other
                $table->decimal('amount', 10, 2);
                $table->date('date_incurred');
                $table->string('payment_method')->default('Cash');
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        // 19. Create Bank Accounts Table
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('bank_name');
                $table->string('account_name');
                $table->string('account_number');
                $table->string('branch')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 20. Create Attendances Table
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('employee_id')->unsigned();
                $table->date('date');
                $table->string('status')->default('present'); // present, absent, late
                $table->time('clock_in')->nullable();
                $table->time('clock_out')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'date']);
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }

        // --- Alter existing tables ---

        // user table
        Schema::table('user', function (Blueprint $table) {
            if (!Schema::hasColumn('user', 'role_id')) {
                $table->bigInteger('role_id')->unsigned()->nullable()->after('password');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            }
        });

        // product table
        Schema::table('product', function (Blueprint $table) {
            if (!Schema::hasColumn('product', 'category_id')) {
                $table->bigInteger('category_id')->unsigned()->nullable()->after('id');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('product', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (!Schema::hasColumn('product', 'sku')) {
                $table->string('sku')->nullable()->unique();
            }
            if (!Schema::hasColumn('product', 'barcode')) {
                $table->string('barcode')->nullable();
            }
            if (!Schema::hasColumn('product', 'buying_price')) {
                $table->decimal('buying_price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('product', 'wholesale_price')) {
                $table->decimal('wholesale_price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('product', 'warranty_months')) {
                $table->integer('warranty_months')->default(12);
            }
            if (!Schema::hasColumn('product', 'expire_date')) {
                $table->date('expire_date')->nullable();
            }
            if (!Schema::hasColumn('product', 'image_path')) {
                $table->string('image_path')->nullable();
            }
            if (!Schema::hasColumn('product', 'specifications')) {
                $table->json('specifications')->nullable();
            }
            if (!Schema::hasColumn('product', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('product', 'is_visible')) {
                $table->boolean('is_visible')->default(true);
            }
        });

        // suppliers table alterations
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'tax_number')) {
                $table->string('tax_number')->nullable();
            }
        });

        // repair table alterations
        Schema::table('repair', function (Blueprint $table) {
            if (!Schema::hasColumn('repair', 'repair_job_no')) {
                $table->string('repair_job_no')->nullable()->unique();
            }
            if (!Schema::hasColumn('repair', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('repair', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('repair', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_model')) {
                $table->string('device_model')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_serial')) {
                $table->string('device_serial')->nullable();
            }
            if (!Schema::hasColumn('repair', 'estimate_cost')) {
                $table->decimal('estimate_cost', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'final_cost')) {
                $table->decimal('final_cost', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'assigned_technician_id')) {
                $table->bigInteger('assigned_technician_id')->unsigned()->nullable();
                $table->foreign('assigned_technician_id')->references('id')->on('employees')->onDelete('set null');
            }

            // Detailed layout columns
            if (!Schema::hasColumn('repair', 'customer_whatsapp')) {
                $table->string('customer_whatsapp')->nullable();
            }
            if (!Schema::hasColumn('repair', 'customer_address')) {
                $table->text('customer_address')->nullable();
            }
            if (!Schema::hasColumn('repair', 'customer_nic')) {
                $table->string('customer_nic')->nullable();
            }
            if (!Schema::hasColumn('repair', 'customer_company')) {
                $table->string('customer_company')->nullable();
            }
            if (!Schema::hasColumn('repair', 'referred_by')) {
                $table->string('referred_by')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_brand')) {
                $table->string('device_brand')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_color')) {
                $table->string('device_color')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_processor')) {
                $table->string('device_processor')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_storage')) {
                $table->string('device_storage')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_ram')) {
                $table->string('device_ram')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_display_size')) {
                $table->string('device_display_size')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_battery')) {
                $table->string('device_battery')->nullable();
            }
            if (!Schema::hasColumn('repair', 'device_charger_watt')) {
                $table->string('device_charger_watt')->nullable();
            }
            if (!Schema::hasColumn('repair', 'physical_condition')) {
                $table->json('physical_condition')->nullable();
            }
            if (!Schema::hasColumn('repair', 'physical_condition_other')) {
                $table->text('physical_condition_other')->nullable();
            }
            if (!Schema::hasColumn('repair', 'accessories_received')) {
                $table->json('accessories_received')->nullable();
            }
            if (!Schema::hasColumn('repair', 'accessories_other')) {
                $table->text('accessories_other')->nullable();
            }
            if (!Schema::hasColumn('repair', 'windows_password')) {
                $table->string('windows_password')->nullable();
            }
            if (!Schema::hasColumn('repair', 'bios_password')) {
                $table->string('bios_password')->nullable();
            }
            if (!Schema::hasColumn('repair', 'bitlocker_status')) {
                $table->string('bitlocker_status')->default('OFF');
            }
            if (!Schema::hasColumn('repair', 'data_backup_required')) {
                $table->boolean('data_backup_required')->default(false);
            }
            if (!Schema::hasColumn('repair', 'customer_accept_data_loss')) {
                $table->boolean('customer_accept_data_loss')->default(false);
            }
            if (!Schema::hasColumn('repair', 'technical_inspection')) {
                $table->json('technical_inspection')->nullable();
            }
            if (!Schema::hasColumn('repair', 'chip_level_repair_notes')) {
                $table->json('chip_level_repair_notes')->nullable();
            }
            if (!Schema::hasColumn('repair', 'board_model')) {
                $table->string('board_model')->nullable();
            }
            if (!Schema::hasColumn('repair', 'freelancer_technician')) {
                $table->string('freelancer_technician')->nullable();
            }
            if (!Schema::hasColumn('repair', 'sent_date')) {
                $table->date('sent_date')->nullable();
            }
            if (!Schema::hasColumn('repair', 'return_date')) {
                $table->date('return_date')->nullable();
            }
            if (!Schema::hasColumn('repair', 'inspection_fee')) {
                $table->decimal('inspection_fee', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'advance_payment')) {
                $table->decimal('advance_payment', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('repair', 'collected_by')) {
                $table->string('collected_by')->nullable();
            }
            if (!Schema::hasColumn('repair', 'date_collected')) {
                $table->date('date_collected')->nullable();
            }
            if (!Schema::hasColumn('repair', 'remaining_balance_paid')) {
                $table->decimal('remaining_balance_paid', 10, 2)->default(0.00);
            }
        });

        // invoices table alterations
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique();
            }
            if (!Schema::hasColumn('invoices', 'customer_id')) {
                $table->integer('customer_id')->nullable();
                $table->foreign('customer_id')->references('id')->on('user')->onDelete('set null');
            }
            if (!Schema::hasColumn('invoices', 'user_id')) {
                $table->integer('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
            }
            if (!Schema::hasColumn('invoices', 'employee_id')) {
                $table->bigInteger('employee_id')->unsigned()->nullable();
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            }
            if (!Schema::hasColumn('invoices', 'bank_account_id')) {
                $table->bigInteger('bank_account_id')->unsigned()->nullable();
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
            }
            if (!Schema::hasColumn('invoices', 'repair_id')) {
                $table->integer('repair_id')->nullable();
                $table->foreign('repair_id')->references('id')->on('repair')->onDelete('set null');
            }
            if (!Schema::hasColumn('invoices', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'sale_type')) {
                $table->string('sale_type')->default('Shop');
            }
            if (!Schema::hasColumn('invoices', 'special_note')) {
                $table->text('special_note')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'is_tax_invoice')) {
                $table->boolean('is_tax_invoice')->default(false);
            }
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'tax')) {
                $table->decimal('tax', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'global_discount_percentage')) {
                $table->decimal('global_discount_percentage', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'global_discount_amount')) {
                $table->decimal('global_discount_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'service_charges')) {
                $table->decimal('service_charges', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'total')) {
                $table->decimal('total', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->default('Cash');
            }
            if (!Schema::hasColumn('invoices', 'is_paid')) {
                $table->boolean('is_paid')->default(true);
            }
            if (!Schema::hasColumn('invoices', 'customer_paid')) {
                $table->decimal('customer_paid', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0.00);
            }
        });

        // appointment table alterations
        Schema::table('appointment', function (Blueprint $table) {
            if (!Schema::hasColumn('appointment', 'appointment_no')) {
                $table->string('appointment_no')->nullable()->unique();
            }
            if (!Schema::hasColumn('appointment', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('appointment', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('appointment', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            if (!Schema::hasColumn('appointment', 'appointment_time')) {
                $table->datetime('appointment_time')->nullable();
            }
            if (!Schema::hasColumn('appointment', 'reason')) {
                $table->text('reason')->nullable();
            }
        });

        // --- Recreate Triggers to support both legacy and Neuro fields ---

        // 1. product table sync triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_insert`");
        DB::unprepared("
            CREATE TRIGGER `product_before_insert` BEFORE INSERT ON `product`
            FOR EACH ROW
            BEGIN
                -- Sync stock and stock_quantity
                IF NEW.stock_quantity IS NULL THEN
                    SET NEW.stock_quantity = NEW.stock;
                ELSE
                    SET NEW.stock = NEW.stock_quantity;
                END IF;

                -- Sync price and selling_price
                IF NEW.selling_price IS NULL THEN
                    SET NEW.selling_price = NEW.price;
                ELSE
                    SET NEW.price = NEW.selling_price;
                END IF;

                -- Sync buying_price and cost_price
                IF NEW.buying_price IS NULL OR NEW.buying_price = 0 THEN
                    SET NEW.buying_price = NEW.cost_price;
                ELSE
                    SET NEW.cost_price = NEW.buying_price;
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_update`");
        DB::unprepared("
            CREATE TRIGGER `product_before_update` BEFORE UPDATE ON `product`
            FOR EACH ROW
            BEGIN
                -- Sync stock and stock_quantity
                IF NOT (NEW.stock_quantity <=> OLD.stock_quantity) THEN
                    SET NEW.stock = NEW.stock_quantity;
                ELSEIF NOT (NEW.stock <=> OLD.stock) THEN
                    SET NEW.stock_quantity = NEW.stock;
                END IF;

                -- Sync price and selling_price
                IF NOT (NEW.selling_price <=> OLD.selling_price) THEN
                    SET NEW.price = NEW.selling_price;
                ELSEIF NOT (NEW.price <=> OLD.price) THEN
                    SET NEW.selling_price = NEW.price;
                END IF;

                -- Sync buying_price and cost_price
                IF NOT (NEW.buying_price <=> OLD.buying_price) THEN
                    SET NEW.cost_price = NEW.buying_price;
                ELSEIF NOT (NEW.cost_price <=> OLD.cost_price) THEN
                    SET NEW.buying_price = NEW.cost_price;
                END IF;
            END
        ");

        // 2. repair table sync triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_insert`");
        DB::unprepared("
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
                    IF NEW.device_model IS NOT NULL THEN
                        SET NEW.laptop_brand = COALESCE(NEW.device_brand, '');
                        SET NEW.laptop_model = NEW.device_model;
                    ELSE
                        SET NEW.laptop_brand = COALESCE(NEW.device, '');
                        SET NEW.laptop_model = '';
                    END IF;
                ELSE
                    SET NEW.device = CONCAT(COALESCE(NEW.laptop_brand, ''), ' ', COALESCE(NEW.laptop_model, ''));
                END IF;

                -- Sync device model/brand with Neuro equivalents
                IF NEW.device_model IS NULL THEN
                    SET NEW.device_model = NEW.laptop_model;
                END IF;
                IF NEW.device_brand IS NULL THEN
                    SET NEW.device_brand = NEW.laptop_brand;
                END IF;

                -- Sync device_serial and serial_number
                IF NEW.device_serial IS NULL THEN
                    IF NEW.serial_number IS NOT NULL THEN
                        SET NEW.device_serial = NEW.serial_number;
                    ELSE
                        SET NEW.device_serial = NEW.serialNumber;
                    END IF;
                END IF;
                IF NEW.serial_number IS NULL THEN
                    SET NEW.serial_number = NEW.device_serial;
                END IF;
                IF NEW.serialNumber IS NULL THEN
                    SET NEW.serialNumber = NEW.serial_number;
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
                IF NEW.final_price IS NULL OR NEW.final_price = 0 THEN
                    SET NEW.final_price = NEW.cost;
                ELSE
                    SET NEW.cost = NEW.final_price;
                END IF;
                IF NEW.final_cost IS NULL OR NEW.final_cost = 0 THEN
                    SET NEW.final_cost = NEW.final_price;
                END IF;

                -- Sync estimate_cost and cost
                IF NEW.estimate_cost IS NULL OR NEW.estimate_cost = 0 THEN
                    SET NEW.estimate_cost = NEW.cost;
                END IF;

                -- Sync completionDate and completed_at
                IF NEW.completed_at IS NULL THEN
                    SET NEW.completed_at = NEW.completionDate;
                ELSE
                    SET NEW.completionDate = NEW.completed_at;
                END IF;

                -- Sync status and repair_status
                IF NEW.repair_status IS NULL THEN
                    SET NEW.repair_status = LOWER(NEW.status);
                ELSE
                    SET NEW.status = UPPER(NEW.repair_status);
                END IF;

                -- Generate job_number and repair_job_no if null
                IF NEW.job_number IS NULL THEN
                    SELECT Auto_increment INTO @next_id FROM information_schema.tables WHERE table_name='repair' AND table_schema=DATABASE();
                    SET NEW.job_number = CONCAT('PWCRJ', LPAD(COALESCE(@next_id, 1), 6, '0'));
                END IF;
                IF NEW.repair_job_no IS NULL THEN
                    SET NEW.repair_job_no = NEW.job_number;
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_update`");
        DB::unprepared("
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

                -- Sync device model / laptop model
                IF NOT (NEW.device_model <=> OLD.device_model) THEN
                    SET NEW.laptop_model = NEW.device_model;
                    SET NEW.device = CONCAT(COALESCE(NEW.laptop_brand, ''), ' ', COALESCE(NEW.device_model, ''));
                ELSEIF NOT (NEW.laptop_model <=> OLD.laptop_model) THEN
                    SET NEW.device_model = NEW.laptop_model;
                    SET NEW.device = CONCAT(COALESCE(NEW.laptop_brand, ''), ' ', COALESCE(NEW.laptop_model, ''));
                ELSEIF NOT (NEW.device <=> OLD.device) THEN
                    SET NEW.laptop_brand = NEW.device;
                    SET NEW.device_model = '';
                    SET NEW.laptop_model = '';
                END IF;

                -- Sync device_serial and serial_number
                IF NOT (NEW.device_serial <=> OLD.device_serial) THEN
                    SET NEW.serial_number = NEW.device_serial;
                    SET NEW.serialNumber = NEW.device_serial;
                ELSEIF NOT (NEW.serial_number <=> OLD.serial_number) THEN
                    SET NEW.device_serial = NEW.serial_number;
                    SET NEW.serialNumber = NEW.serial_number;
                ELSEIF NOT (NEW.serialNumber <=> OLD.serialNumber) THEN
                    SET NEW.device_serial = NEW.serialNumber;
                    SET NEW.serial_number = NEW.serialNumber;
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

                -- Sync final_price and final_cost
                IF NOT (NEW.final_cost <=> OLD.final_cost) THEN
                    SET NEW.final_price = NEW.final_cost;
                    SET NEW.cost = NEW.final_cost;
                ELSEIF NOT (NEW.final_price <=> OLD.final_price) THEN
                    SET NEW.final_cost = NEW.final_price;
                    SET NEW.cost = NEW.final_price;
                ELSEIF NOT (NEW.cost <=> OLD.cost) THEN
                    SET NEW.final_price = NEW.cost;
                    SET NEW.final_cost = NEW.cost;
                END IF;

                -- Sync status and repair_status
                IF NOT (NEW.repair_status <=> OLD.repair_status) THEN
                    SET NEW.status = UPPER(NEW.repair_status);
                ELSEIF NOT (NEW.status <=> OLD.status) THEN
                    SET NEW.repair_status = LOWER(NEW.status);
                END IF;
                
                -- Sync repair_job_no and job_number
                IF NOT (NEW.repair_job_no <=> OLD.repair_job_no) THEN
                    SET NEW.job_number = NEW.repair_job_no;
                ELSEIF NOT (NEW.job_number <=> OLD.job_number) THEN
                    SET NEW.repair_job_no = NEW.job_number;
                END IF;
            END
        ");

        // 3. invoices table triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_insert`");
        DB::unprepared("
            CREATE TRIGGER `invoices_before_insert` BEFORE INSERT ON `invoices`
            FOR EACH ROW
            BEGIN
                -- Sync repair_job_id and repair_id
                IF NEW.repair_id IS NULL THEN
                    SET NEW.repair_id = NEW.repair_job_id;
                ELSE
                    SET NEW.repair_job_id = NEW.repair_id;
                END IF;

                -- Sync total_amount and total
                IF NEW.total IS NULL OR NEW.total = 0 THEN
                    SET NEW.total = NEW.total_amount;
                ELSE
                    SET NEW.total_amount = NEW.total;
                END IF;

                -- Sync paid_amount and customer_paid
                IF NEW.customer_paid IS NULL OR NEW.customer_paid = 0 THEN
                    SET NEW.customer_paid = NEW.paid_amount;
                ELSE
                    SET NEW.paid_amount = NEW.customer_paid;
                END IF;

                -- Sync status
                IF NEW.status IS NULL THEN
                    SET NEW.status = IF(NEW.is_paid = 1, 'paid', 'unpaid');
                END IF;

                -- Generate invoice_number if null
                IF NEW.invoice_number IS NULL THEN
                    SELECT Auto_increment INTO @next_id FROM information_schema.tables WHERE table_name='invoices' AND table_schema=DATABASE();
                    SET NEW.invoice_number = CONCAT('INV-', LPAD(COALESCE(@next_id, 1), 6, '0'));
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_update`");
        DB::unprepared("
            CREATE TRIGGER `invoices_before_update` BEFORE UPDATE ON `invoices`
            FOR EACH ROW
            BEGIN
                -- Sync repair_job_id and repair_id
                IF NOT (NEW.repair_id <=> OLD.repair_id) THEN
                    SET NEW.repair_job_id = NEW.repair_id;
                ELSEIF NOT (NEW.repair_job_id <=> OLD.repair_job_id) THEN
                    SET NEW.repair_id = NEW.repair_job_id;
                END IF;

                -- Sync total_amount and total
                IF NOT (NEW.total <=> OLD.total) THEN
                    SET NEW.total_amount = NEW.total;
                ELSEIF NOT (NEW.total_amount <=> OLD.total_amount) THEN
                    SET NEW.total = NEW.total_amount;
                END IF;

                -- Sync paid_amount and customer_paid
                IF NOT (NEW.customer_paid <=> OLD.customer_paid) THEN
                    SET NEW.paid_amount = NEW.customer_paid;
                ELSEIF NOT (NEW.paid_amount <=> OLD.paid_amount) THEN
                    SET NEW.customer_paid = NEW.paid_amount;
                END IF;
            END
        ");

        // 4. appointment table triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `appointment_before_insert`");
        DB::unprepared("
            CREATE TRIGGER `appointment_before_insert` BEFORE INSERT ON `appointment`
            FOR EACH ROW
            BEGIN
                -- Sync appointment_time and date
                IF NEW.date IS NULL AND NEW.appointment_time IS NOT NULL THEN
                    SET NEW.date = NEW.appointment_time;
                    SET NEW.time = TIME_FORMAT(NEW.appointment_time, '%H:%i');
                ELSEIF NEW.date IS NOT NULL AND NEW.appointment_time IS NULL THEN
                    SET NEW.appointment_time = NEW.date;
                END IF;

                -- Sync reason and issue
                IF NEW.reason IS NULL THEN
                    SET NEW.reason = NEW.issue;
                ELSE
                    SET NEW.issue = NEW.reason;
                END IF;

                -- Sync status
                IF NEW.status IS NULL THEN
                    SET NEW.status = 'PENDING';
                END IF;

                -- Generate appointment_no if null
                IF NEW.appointment_no IS NULL THEN
                    SELECT Auto_increment INTO @next_id FROM information_schema.tables WHERE table_name='appointment' AND table_schema=DATABASE();
                    SET NEW.appointment_no = CONCAT('APT-', LPAD(COALESCE(@next_id, 1), 6, '0'));
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS `appointment_before_update`");
        DB::unprepared("
            CREATE TRIGGER `appointment_before_update` BEFORE UPDATE ON `appointment`
            FOR EACH ROW
            BEGIN
                -- Sync appointment_time and date
                IF NOT (NEW.appointment_time <=> OLD.appointment_time) THEN
                    SET NEW.date = NEW.appointment_time;
                    SET NEW.time = TIME_FORMAT(NEW.appointment_time, '%H:%i');
                ELSEIF NOT (NEW.date <=> OLD.date) THEN
                    SET NEW.appointment_time = NEW.date;
                END IF;

                -- Sync reason and issue
                IF NOT (NEW.reason <=> OLD.reason) THEN
                    SET NEW.issue = NEW.reason;
                ELSEIF NOT (NEW.issue <=> OLD.issue) THEN
                    SET NEW.reason = NEW.issue;
                END IF;
            END
        ");

        // --- Roles & Permissions Seeding ---

        $rolesMapping = [
            'admin' => 'Admin',
            'manager' => 'Manager',
            'cashier' => 'Cashier',
            'technician' => 'Technician'
        ];

        $roleIds = [];
        foreach ($rolesMapping as $slug => $name) {
            $id = DB::table('roles')->insertGetId([
                'name' => $name,
                'description' => "Default system {$slug} role.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $roleIds[$slug] = $id;
        }

        // Map existing users to role_ids
        $users = DB::table('user')->get();
        foreach ($users as $user) {
            $oldRole = strtolower($user->role ?? '');
            $assignedRoleId = $roleIds[$oldRole] ?? null;
            if ($assignedRoleId) {
                DB::table('user')->where('id', $user->id)->update([
                    'role_id' => $assignedRoleId
                ]);
            }
        }

        // Standard permissions list
        $modules = [
            'Invoices' => ['create-invoices', 'read-invoices', 'update-invoices', 'delete-invoices'],
            'Quotations' => ['create-quotations', 'read-quotations', 'update-quotations', 'delete-quotations'],
            'Customers' => ['create-customers', 'read-customers', 'update-customers', 'delete-customers'],
            'Inventory' => ['create-grn', 'read-grn', 'update-grn', 'delete-grn', 'create-products', 'read-products', 'update-products', 'delete-products', 'create-categories', 'read-categories', 'update-categories', 'delete-categories'],
            'Suppliers' => ['create-suppliers', 'read-suppliers', 'update-suppliers', 'delete-suppliers'],
            'Repairs' => ['create-repairs', 'read-repairs', 'update-repairs', 'delete-repairs'],
            'Warranty' => ['create-warranty', 'read-warranty', 'update-warranty', 'delete-warranty'],
            'Appointments' => ['create-appointments', 'read-appointments', 'update-appointments', 'delete-appointments'],
            'Expenses' => ['create-expenses', 'read-expenses', 'update-expenses', 'delete-expenses'],
            'Returns' => ['create-returns', 'read-returns', 'update-returns', 'delete-returns'],
            'Employees' => ['create-employees', 'read-employees', 'update-employees', 'delete-employees'],
            'Salaries' => ['create-salaries', 'read-salaries', 'update-salaries', 'delete-salaries'],
            'Users' => ['create-users', 'read-users', 'update-users', 'delete-users'],
            'Roles' => ['create-roles', 'read-roles', 'update-roles', 'delete-roles'],
            'Settings' => ['create-bank-accounts', 'read-bank-accounts', 'update-bank-accounts', 'delete-bank-accounts']
        ];

        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $perm,
                    'module' => $module,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Assign to Admin role
                DB::table('permission_role')->insert([
                    'role_id' => $roleIds['admin'],
                    'permission_id' => $permissionId
                ]);

                // Assign to Manager role (almost all except user/role management)
                if ($module !== 'Users' && $module !== 'Roles') {
                    DB::table('permission_role')->insert([
                        'role_id' => $roleIds['manager'],
                        'permission_id' => $permissionId
                    ]);
                }

                // Assign to Cashier role (Invoices, Quotations, Customers, Appointments, read-products, read-repairs)
                if (in_array($module, ['Invoices', 'Quotations', 'Customers', 'Appointments']) || 
                    in_array($perm, ['read-products', 'read-repairs'])) {
                    DB::table('permission_role')->insert([
                        'role_id' => $roleIds['cashier'],
                        'permission_id' => $permissionId
                    ]);
                }

                // Assign to Technician role (read-repairs, update-repairs, read-warranty, update-warranty, read-products)
                if (in_array($perm, ['read-repairs', 'update-repairs', 'read-warranty', 'update-warranty', 'read-products'])) {
                    DB::table('permission_role')->insert([
                        'role_id' => $roleIds['technician'],
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop sync triggers
        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `product_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `repair_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `appointment_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `appointment_before_update`");

        // Alter tables to drop added columns
        Schema::table('appointment', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_no', 'customer_name', 'customer_phone', 'customer_email',
                'appointment_time', 'reason'
            ]);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['bank_account_id']);
            $table->dropForeign(['repair_id']);
            $table->dropColumn([
                'invoice_number', 'customer_id', 'user_id', 'employee_id', 'bank_account_id',
                'repair_id', 'title', 'sale_type', 'special_note', 'due_date',
                'is_tax_invoice', 'subtotal', 'tax', 'discount', 'global_discount_percentage',
                'global_discount_amount', 'service_charges', 'total', 'payment_method',
                'is_paid', 'customer_paid', 'balance'
            ]);
        });

        Schema::table('repair', function (Blueprint $table) {
            $table->dropForeign(['assigned_technician_id']);
            $table->dropColumn([
                'repair_job_no', 'customer_name', 'customer_phone', 'customer_email',
                'device_model', 'device_serial', 'estimate_cost', 'final_cost', 'assigned_technician_id',
                'customer_whatsapp', 'customer_address', 'customer_nic', 'customer_company', 'referred_by',
                'device_brand', 'device_color', 'device_processor', 'device_storage', 'device_ram',
                'device_display_size', 'device_battery', 'device_charger_watt', 'physical_condition',
                'physical_condition_other', 'accessories_received', 'accessories_other', 'windows_password',
                'bios_password', 'bitlocker_status', 'data_backup_required', 'customer_accept_data_loss',
                'technical_inspection', 'chip_level_repair_notes', 'board_model', 'freelancer_technician',
                'sent_date', 'return_date', 'inspection_fee', 'advance_payment', 'balance',
                'collected_by', 'date_collected', 'remaining_balance_paid'
            ]);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'phone', 'email', 'address', 'tax_number']);
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id', 'slug', 'sku', 'barcode', 'buying_price', 'wholesale_price',
                'warranty_months', 'expire_date', 'image_path', 'specifications', 'is_featured', 'is_visible'
            ]);
        });

        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id']);
        });

        // Drop tables in dependency order
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('repair_items');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('product_serials');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('grns');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('categories');
    }
};
