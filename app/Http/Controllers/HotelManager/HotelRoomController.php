<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HotelRoom;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use App\Http\Requests\HotelRoom\UpdateHotelRoomRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // استيراد Trait AuthorizesRequests


class HotelRoomController extends Controller
{
    use AuthorizesRequests; // استخدام Trait AuthorizesRequests

    /**
     * Display a listing of rooms within hotels managed by the authenticated user.
     */
    public function index(): View
    {
        // صلاحية عرض قائمة الغرف في لوحة مدير الفندق يتم التحكم بها بواسطة Route Middleware
        // 'can:access-hotelmanager-panel' على مستوى مجموعة المسارات.
        // الاستعلام نفسه يقوم بالفلترة بناءً على الفنادق التي يديرها المستخدم.

        $user = Auth::user();

        // احصل على معرفات الفنادق التي يديرها هذا المستخدم
        $managedHotelIds = $user->hotelsManaged->pluck('id');

        // جلب الغرف التي تنتمي إلى هذه الفنادق، مع ترقيم الصفحات وتحميل العلاقات اللازمة للعرض
        $rooms = HotelRoom::whereIn('hotel_id', $managedHotelIds)
                           ->with('hotel', 'type') // تحميل علاقات الفندق ونوع الغرفة
                           ->paginate(10);

        return view('hotelmanager.hotel_rooms.index', compact('rooms'));
    }

    /**
     * Display the specified room within a managed hotel.
     */
public function show(HotelRoom $hotelRoom): View
    {
        $hotelRoom->load('hotel'); // ** تحميل العلاقة قبل التحقق **
        // $this->authorize('manage-hotel-room', $hotelRoom); // استدعاء الـ Gate

        $hotelRoom->load('type');
        return view('hotelmanager.hotel_rooms.show', compact('hotelRoom'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(HotelRoom $hotelRoom): View
    {
        $hotelRoom->load('hotel'); // ** تحميل العلاقة قبل التحقق **
        // $this->authorize('manage-hotel-room', $hotelRoom); // استدعاء الـ Gate

        $roomTypes = HotelRoomType::all();
        $managedHotels = Auth::user()->hotelsManaged;
        return view('hotelmanager.hotel_rooms.edit', compact('hotelRoom', 'roomTypes', 'managedHotels'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(UpdateHotelRoomRequest $request, HotelRoom $hotelRoom): RedirectResponse
    {
        // ** تحميل علاقة 'hotel' قبل التحقق من الصلاحية **
        $hotelRoom->load('hotel'); // ضروري لأن الـ Gate يتحقق من $hotelRoom->hotel

        // استخدام الـ Gate للتحقق من صلاحية تحديث هذه الغرفة
        // $this->authorize('manage-hotel-room', $hotelRoom); // هذا السطر سيستدعي الـ Gate

        // التحقق من الصحة يتم بواسطة UpdateHotelRoomRequest ($request->validated())

        // تحديث بيانات الغرفة
        // تأكد أن الحقول المسموح بتحديثها موجودة في $fillable في HotelRoom model
        $hotelRoom->update($request->validated());

        // إعادة التوجيه بعد التحديث
        return redirect()->route('hotelmanager.hotel-rooms.index') // أو redirect()->route('hotelmanager.hotel-rooms.show', $hotelRoom)
                         ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(HotelRoom $hotelRoom): RedirectResponse
    {
        // ** تحميل علاقة 'hotel' قبل التحقق من الصلاحية **
        $hotelRoom->load('hotel'); // ضروري لأن الـ Gate يتحقق من $hotelRoom->hotel

        // استخدام الـ Gate للتحقق من صلاحية حذف هذه الغرفة
        // $this->authorize('manage-hotel-room', $hotelRoom); // هذا السطر سيستدعي الـ Gate

        // حذف الغرفة
        $hotelRoom->delete();

        // إعادة التوجيه بعد الحذف
        return redirect()->route('hotelmanager.hotel-rooms.index')
                         ->with('success', 'Room deleted successfully.');
    }

    /**
     * Show the form for creating a new resource. (Not used by 'only' routes)
     */
    public function create()
    {
        // بما أن المسارات معرفة بـ ->only(['index', 'show', 'edit', 'update'])
        // فهذه الدالة لن تُستخدم ولن يتم الوصول إليها عبر الـ Route Resource
        abort(404); // يمكنك ترك هذا أو حذفه، لن يتم الوصول إليه.
    }

    /**
     * Store a newly created resource in storage. (Not used by 'only' routes)
     */
    public function store(Request $request)
    {
        // بما أن المسارات معرفة بـ ->only(['index', 'show', 'edit', 'update'])
        // فهذه الدالة لن تُستخدم ولن يتم الوصول إليها عبر الـ Route Resource
         abort(404); // يمكنك ترك هذا أو حذفه، لن يتم الوصول إليه.
    }
}