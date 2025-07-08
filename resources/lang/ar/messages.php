<?php

return [
    // General terms
    'Dashboard' => 'لوحة التحكم',
    'Users' => 'المستخدمون',
    'Products' => 'المنتجات',
    'Sites' => 'المواقع السياحية', // Used in admin layout
    'Hotels' => 'الفنادق',
    'Articles' => 'المقالات',
    'Profile' => 'الملف الشخصي',
    'Log Out' => 'تسجيل الخروج',
    'Log In' => 'تسجيل الدخول',
    'Register' => 'تسجيل حساب جديد',
    'Back to Products List' => 'العودة إلى قائمة المنتجات',
    'Back to Orders List' => 'العودة إلى قائمة الطلبات',
    'Back to Hotels List' => 'العودة إلى قائمة الفنادق',
    'Back to Rooms List' => 'العودة إلى قائمة الغرف',
    'Back to Bookings List' => 'العودة إلى قائمة الحجوزات',
    'N/A' => 'غير متوفر', // Not Applicable

    // Layout & Navigation
    'App' => 'التطبيق', // App name placeholder
    'Product Categories' => 'فئات المنتجات',
    'Site Categories' => 'فئات المواقع',
    'Activities' => 'الأنشطة', // Tourist Activities
    'Room Types' => 'أنواع الغرف',
    'Product Orders' => 'طلبات المنتجات',
    'Experiences' => 'التجارب', // Site Experiences
    'Ratings' => 'التقييمات',
    'Comments' => 'التعليقات',

    // Auth & Login (from Volt Component and AuthController logic)
    'Email' => 'البريد الإلكتروني', // Primary login field label (if using email)
    'Password' => 'كلمة المرور',
    'Remember me' => 'تذكرني',
    'Forgot your password?' => 'هل نسيت كلمة المرور؟',
    'Log in' => 'دخول', // Button text
    'Quick Login (Seeded Users)' => 'تسجيل الدخول السريع (مستخدمي Seeder)',
    'Admin' => 'مدير النظام',
    'Tourist' => 'سائح',
    'Vendor' => 'بائع',
    'Hotel Manager' => 'مدير فندق',
    'API JSON Input (Development)' => 'إدخال JSON للـ API (للتطوير)',
    'Paste JSON Payload Here' => 'الصق حمولة JSON هنا',
    'Process JSON (Log)' => 'معالجة JSON (سجل)',
    'JSON data received and logged.' => 'تم استلام بيانات JSON وتسجيلها.',
    'Error processing JSON data: Invalid JSON data.' => 'خطأ في معالجة بيانات JSON: بيانات JSON غير صالحة.', // Example message for JSON processing
    'Note: Processing JSON here logs/validates it. Sending to API requires client-side JavaScript or further Livewire logic.' => 'ملاحظة: معالجة JSON هنا تسجلها/تتحقق منها. الإرسال إلى الـ API يتطلب جافاسكربت في الواجهة الأمامية أو منطق إضافي في Livewire.',


    // Admin Dashboard Summary (from Admin\DashboardController and view)
    'Welcome to the Admin Panel!' => 'مرحباً بك في لوحة تحكم مدير النظام!',
    'Total Users' => 'إجمالي المستخدمين',
    'Total Products' => 'إجمالي المنتجات',
    'Pending Orders' => 'الطلبات المعلقة',
    'Manage Sections' => 'إدارة الأقسام',

    // Vendor Dashboard Summary (from Vendor\DashboardController and view)
    'Welcome, Vendor!' => 'مرحباً بك أيها البائع!',
    'Your Products' => 'منتجاتك',
    'Total Orders' => 'إجمالي الطلبات',
    'Orders' => 'الطلبات', // Another key for orders
    'Manage Your Products & Orders' => 'إدارة منتجاتك وطلباتك',

    // Hotel Manager Dashboard Summary (from HotelManager\DashboardController and view)
    'Welcome, Hotel Manager!' => 'مرحباً بك أيها مدير الفندق!',
    'Managed Hotels' => 'الفنادق التي تديرها',
    'Total Rooms Managed' => 'إجمالي الغرف التي تديرها',
    'Upcoming Bookings' => 'الحجوزات القادمة',
    'Manage Your Hotels & Bookings' => 'إدارة فنادقك وحجوزاتك',
    'Your Hotels' => 'فنادقك', // Another key
    'Your Rooms' => 'غرفك', // Another key

    // Vendor Product Management (from Vendor\ProductController and views)
    'Add New Product' => 'إضافة منتج جديد',
    'Edit Product' => 'تعديل المنتج',
    'Product Details' => 'تفاصيل المنتج',
    'Image' => 'الصورة',
    'Name' => 'الاسم',
    'Price' => 'السعر',
    'Stock' => 'المخزون',
    'Available' => 'متاح', // For product availability
    'Actions' => 'الإجراءات',
    'View' => 'عرض',
    'Edit' => 'تعديل',
    'Delete' => 'حذف',
    'No products found.' => 'لم يتم العثور على منتجات.',
    'Product Name' => 'اسم المنتج',
    'Stock Quantity' => 'كمية المخزون',
    'Color (Optional)' => 'اللون (اختياري)',
    'Description' => 'الوصف',
    'Main Image (Optional)' => 'الصورة الرئيسية (اختياري)',
    'Available for Sale' => 'متاح للبيع',
    'Create Product' => 'إنشاء المنتج',
    'Update Product' => 'تحديث المنتج',
    'Select Category' => 'اختر الفئة',
    'Category' => 'الفئة',

    // Vendor Product Orders (from Vendor\ProductOrderController and views)
    'Orders For Your Products' => 'طلبات منتجاتك',
    'Order ID' => 'رقم الطلب',
    'Customer' => 'العميل',
    'Date' => 'التاريخ',
    'Items Count (Your Products)' => 'عدد الأصناف (منتجاتك)',
    'Total Amount' => 'المبلغ الإجمالي',
    'Status' => 'الحالة',
    'View Details' => 'عرض التفاصيل',
    'No orders found for your products.' => 'لم يتم العثور على طلبات لمنتجاتك.',
    'Items in This Order' => 'الأصناف في هذا الطلب',
    'Product' => 'المنتج',
    'Quantity' => 'الكمية',
    'Price at Purchase' => 'السعر عند الشراء',
    'Shipping Address' => 'عنوان الشحن',
    'Payment Status' => 'حالة الدفع',
    'Payment Transaction ID' => 'معرف معاملة الدفع',


    // Hotel Manager Hotel Management (from HotelManager\HotelController and views)
    'Edit Hotel Details' => 'تعديل تفاصيل الفندق',
    'Hotel Details' => 'تفاصيل الفندق',
    'City' => 'المدينة',
    'Star Rating' => 'تصنيف النجوم',
    'Rooms Count' => 'عدد الغرف',
    'Address Line 1' => 'عنوان الشارع 1',
    'Contact Phone' => 'هاتف التواصل',
    'Contact Email' => 'بريد التواصل',
    'Location Coordinates' => 'إحداثيات الموقع',
    'Rooms in This Hotel' => 'الغرف في هذا الفندق',
    'Update Hotel' => 'تحديث الفندق',

    // Hotel Manager Room Management (from HotelManager\HotelRoomController and views)
    'Managed Hotel Rooms' => 'الغرف التي تديرها',
    'Room Details' => 'تفاصيل الغرفة',
    'Edit Room' => 'تعديل الغرفة',
    'Add New Room' => 'إضافة غرفة جديدة',
    'Hotel' => 'الفندق', // Reused key
    'Room Number' => 'رقم الغرفة',
    'Room Type' => 'نوع الغرفة',
    'Price Per Night' => 'سعر الليلة',
    'Max Occupancy' => 'الحد الأقصى للإشغال',
    'Area (sqm)' => 'المساحة (م2)', // Simplified from Area (sqm, Optional)
    'Available for Booking' => 'متاحة للحجز',
    'No rooms found for this hotel.' => 'لم يتم العثور على غرف لهذا الفندق.',
    'No rooms found for your managed hotels.' => 'لم يتم العثور على غرف لفنادقك المدارة.',
    'Update Room' => 'تحديث الغرفة',


    // Hotel Manager Booking Management (from HotelManager\HotelBookingController and views)
    'Managed Hotel Bookings' => 'حجوزات الفنادق التي تديرها',
    'Create New Hotel Booking' => 'إنشاء حجز فندق جديد',
    'Booking Details' => 'تفاصيل الحجز',
    'Booking ID' => 'رقم الحجز',
    'Room' => 'الغرفة', // Reused key
    'Check-in' => 'تسجيل الدخول', // Check-in Date
    'Check-out' => 'تسجيل الخروج', // Check-out Date
    'Guests' => 'النزلاء',
    'No bookings found for your managed hotels.' => 'لم يتم العثور على حجوزات لفنادقك المدارة.',
    'Create New Booking' => 'إنشاء حجز جديد', // Button text
    'Select Hotel' => 'اختر الفندق',
    'Select Room' => 'اختر الغرفة',
    'Select Customer' => 'اختر العميل',
    'Check-in Date' => 'تاريخ تسجيل الدخول',
    'Check-out Date' => 'تاريخ تسجيل الخروج',
    'Number of Adults' => 'عدد البالغين',
    'Number of Children (Optional)' => 'عدد الأطفال (اختياري)',
    'Special Requests (Optional)' => 'طلبات خاصة (اختياري)',
    'Update Booking' => 'تحديث الحجز',
    'Booked At' => 'تم الحجز في',

    // Common Statuses (Map these if using __('') on statuses themselves)
    'PendingConfirmation' => 'بانتظار التأكيد',
    'Confirmed' => 'مؤكد',
    'CancelledByUser' => 'ملغى من قبل المستخدم',
    'CancelledByHotel' => 'ملغى من قبل الفندق',
    'Completed' => 'مكتمل',
    'NoShow' => 'لم يحضر',
    'Unpaid' => 'غير مدفوع',
    'Paid' => 'مدفوع',
    'PaymentFailed' => 'فشل الدفع',
    'Refunded' => 'تم رد المبلغ',

    // Example: Validation messages (if using __('') for custom messages in FormRequests)
    // Add entries for keys used in FormRequest messages() method, e.g.:
    'validation.required' => ':attribute مطلوب.', // Default Laravel validation message key
    'validation.string' => ':attribute يجب أن يكون سلسلة نصية.',
    'validation.email' => ':attribute يجب أن يكون عنوان بريد إلكتروني صالح.',
    // ... add other validation message keys ...
    'username.unique' => 'اسم المستخدم هذا موجود بالفعل.', // Custom message example

    // Example: Auth messages (from Laravel's auth.php, copy relevant keys if you publish them)
    'auth.failed' => 'بيانات الاعتماد هذه لا تتطابق مع سجلاتنا.', // Example from default Laravel auth.php
    'Hotel Manager Dashboard' => 'لوحة تحكم مدير الفندق',
    // You will need to gather ALL __('') calls from your project
    // and add them as keys here with their Arabic translations.
];