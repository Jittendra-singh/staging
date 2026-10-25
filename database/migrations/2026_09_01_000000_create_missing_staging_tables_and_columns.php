<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfills tables and columns present in staging database that had no migrations.
     */
    public function up(): void
    {
        // 1. carts
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('event_id');
                $table->text('stripe_session_id')->nullable();
                $table->text('stripe_payment_intent_id')->nullable();
                $table->longText('request_data')->nullable();
                $table->longText('order_details')->nullable();
                $table->longText('ticket_data')->nullable();
                $table->timestamps();
            });
        }

        // 2. event_order_ticket_childs (Base table)
        if (!Schema::hasTable('event_order_ticket_childs')) {
            Schema::create('event_order_ticket_childs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('event_order_ticket_id')->nullable();
                $table->bigInteger('event_id');
                $table->bigInteger('order_id');
                $table->string('child_name', 200);
                $table->float('child_age');
                $table->timestamps();
            });
        }

        // 3. event_finalize_email_logs
        if (!Schema::hasTable('event_finalize_email_logs')) {
            Schema::create('event_finalize_email_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email_count', 255)->nullable();
                $table->string('is_success', 20)->nullable();
                $table->longText('response')->nullable();
                $table->longText('response_code')->nullable();
                $table->longText('request_data')->nullable();
                $table->longText('request_data_size')->nullable();
                $table->timestamps();
            });
        }

        // 4. event_reschedule_logs
        if (!Schema::hasTable('event_reschedule_logs')) {
            Schema::create('event_reschedule_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('event_id');
                $table->bigInteger('user_id')->nullable();
                $table->dateTime('old_start_datetime')->nullable();
                $table->dateTime('old_end_datetime')->nullable();
                $table->date('old_site_publish_date')->nullable();
                $table->date('old_site_depublish_date')->nullable();
                $table->dateTime('new_start_datetime')->nullable();
                $table->dateTime('new_end_datetime')->nullable();
                $table->date('new_site_publish_date')->nullable();
                $table->date('new_site_depublish_date')->nullable();
                $table->longText('note')->nullable();
                $table->string('email_count', 255)->nullable();
                $table->string('is_success', 20)->nullable();
                $table->longText('response')->nullable();
                $table->longText('response_code')->nullable();
                $table->longText('request_data')->nullable();
                $table->longText('request_data_size')->nullable();
                $table->timestamps();
            });
        }

        // 5. event_ticket_notifications
        if (!Schema::hasTable('event_ticket_notifications')) {
            Schema::create('event_ticket_notifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('volunteer_id')->nullable();
                $table->bigInteger('ticket_id');
                $table->text('email')->nullable();
                $table->tinyInteger('is_email_verified')->default(1);
                $table->tinyInteger('is_sent')->default(0);
                $table->timestamps();
            });
        }

        // 6. release_notes
        if (!Schema::hasTable('release_notes')) {
            Schema::create('release_notes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('title');
                $table->string('version', 100)->nullable();
                $table->tinyInteger('type')->default(1);
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->date('release_date')->nullable();
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
            });
        }

        // 7. stripe_logs
        if (!Schema::hasTable('stripe_logs')) {
            Schema::create('stripe_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('order_id')->nullable();
                $table->text('payment_intent')->nullable();
                $table->text('payment_session_id')->nullable();
                $table->text('charge_id')->nullable();
                $table->text('fee_id')->nullable();
                $table->text('log_type')->nullable();
                $table->longText('system_data')->nullable();
                $table->longText('stripe_response')->nullable();
                $table->float('amount')->nullable();
                $table->float('fee')->nullable();
                $table->timestamps();
            });
        }

        // 8. ticket_notification_logs
        if (!Schema::hasTable('ticket_notification_logs')) {
            Schema::create('ticket_notification_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email_count', 255)->nullable();
                $table->string('is_success', 20)->nullable();
                $table->longText('response')->nullable();
                $table->longText('response_code')->nullable();
                $table->longText('request_data')->nullable();
                $table->longText('request_data_size')->nullable();
                $table->timestamps();
            });
        }

        // 9. ticket_resend_waiver_logs
        if (!Schema::hasTable('ticket_resend_waiver_logs')) {
            Schema::create('ticket_resend_waiver_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('ticket_id');
                $table->tinyInteger('link_send_type');
                $table->text('link_send_to_email');
                $table->bigInteger('link_send_by');
                $table->timestamps();
            });
        }

        // 10. volunteer_widgets
        if (!Schema::hasTable('volunteer_widgets')) {
            Schema::create('volunteer_widgets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('volunteer_id');
                $table->string('wid_number', 200);
                $table->string('title', 255);
                $table->text('location')->nullable();
                $table->string('location_lat', 200)->nullable();
                $table->string('location_long', 200)->nullable();
                $table->float('distance')->nullable();
                $table->tinyInteger('type')->default(1);
                $table->text('org_ids')->nullable();
                $table->text('cat_ids')->nullable();
                $table->text('ftr_tag_ids')->nullable();
                $table->text('ftr_event_title')->nullable();
                $table->date('ftr_event_start_dt')->nullable();
                $table->date('ftr_event_end_dt')->nullable();
                $table->string('custom_widget_header', 255)->nullable();
                $table->integer('event_per_page')->default(3);
                $table->text('website')->nullable();
                $table->tinyInteger('empty_behavior')->default(1);
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('is_active')->default(1);
                $table->timestamps();
            });
        }

        // 11. volunteer_widget_logs
        if (!Schema::hasTable('volunteer_widget_logs')) {
            Schema::create('volunteer_widget_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('widget_id');
                $table->tinyInteger('log_type');
                $table->bigInteger('reference_id')->nullable();
                $table->timestamps();
            });
        }

        // Columns backfill for existing tables
        // events
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'finalize_email_count')) {
                $table->integer('finalize_email_count')->default(0);
            }
        });

        // event_order_tickets
        Schema::table('event_order_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_tickets', 'is_manual_checkin')) {
                $table->tinyInteger('is_manual_checkin')->default(0);
            }
            if (!Schema::hasColumn('event_order_tickets', 'is_late_checkin')) {
                $table->tinyInteger('is_late_checkin')->default(0);
            }
            if (!Schema::hasColumn('event_order_tickets', 'waiver_signed_external_document')) {
                $table->text('waiver_signed_external_document')->nullable();
            }
            if (!Schema::hasColumn('event_order_tickets', 'cancel_ticket_error')) {
                $table->longText('cancel_ticket_error')->nullable();
            }
        });

        // event_tickets
        Schema::table('event_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_tickets', 'pass_code')) {
                $table->string('pass_code', 50)->nullable();
            }
        });

        // volunteers
        Schema::table('volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteers', 'release_note_last_seen_at')) {
                $table->dateTime('release_note_last_seen_at')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'stripe_email')) {
                $table->text('stripe_email')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'stripe_account_id')) {
                $table->text('stripe_account_id')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'stripe_payouts_enabled')) {
                $table->tinyInteger('stripe_payouts_enabled')->default(0);
            }
            if (!Schema::hasColumn('volunteers', 'stripe_charges_enabled')) {
                $table->tinyInteger('stripe_charges_enabled')->default(0);
            }
            if (!Schema::hasColumn('volunteers', 'stripe_raw')) {
                $table->longText('stripe_raw')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'stripe_connect_dt')) {
                $table->dateTime('stripe_connect_dt')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'organisation_prev_cleanups_count')) {
                $table->bigInteger('organisation_prev_cleanups_count')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'organisation_prev_volunteers_count')) {
                $table->bigInteger('organisation_prev_volunteers_count')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'organisation_prev_waste_collected_pounds_count')) {
                $table->float('organisation_prev_waste_collected_pounds_count')->nullable();
            }
            if (!Schema::hasColumn('volunteers', 'organisation_prev_volunteer_hours_count')) {
                $table->bigInteger('organisation_prev_volunteer_hours_count')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive reverse by default
    }
};
