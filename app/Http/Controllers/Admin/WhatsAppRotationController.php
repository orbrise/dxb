<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppRotationMessage;
use Illuminate\Http\Request;

class WhatsAppRotationController extends Controller
{
    /**
     * Display list of all rotation messages
     */
    public function index()
    {
        $messages = WhatsAppRotationMessage::ordered()->get();
        return view('admin.whatsapp.rotation-messages', compact('messages'));
    }
    
    /**
     * Store a new rotation message
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:1000'
        ]);
        
        // Get the highest order number
        $maxOrder = WhatsAppRotationMessage::max('order') ?? 0;
        
        WhatsAppRotationMessage::create([
            'message' => $request->message,
            'is_active' => true,
            'order' => $maxOrder + 1
        ]);
        
        return redirect()->route('admin.whatsapp.rotation.index')
            ->with('success', 'Message added successfully!');
    }
    
    /**
     * Update an existing rotation message
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:1000'
        ]);
        
        $rotationMessage = WhatsAppRotationMessage::findOrFail($id);
        $rotationMessage->update([
            'message' => $request->message
        ]);
        
        return redirect()->route('admin.whatsapp.rotation.index')
            ->with('success', 'Message updated successfully!');
    }
    
    /**
     * Toggle message active status
     */
    public function toggleStatus($id)
    {
        $message = WhatsAppRotationMessage::findOrFail($id);
        $message->update([
            'is_active' => !$message->is_active
        ]);
        
        return response()->json([
            'success' => true,
            'is_active' => $message->is_active,
            'message' => $message->is_active ? 'Message activated' : 'Message deactivated'
        ]);
    }
    
    /**
     * Delete a rotation message
     */
    public function destroy($id)
    {
        $message = WhatsAppRotationMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->route('admin.whatsapp.rotation.index')
            ->with('success', 'Message deleted successfully!');
    }
    
    /**
     * Reorder messages via AJAX
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:whatsapp_rotation_messages,id'
        ]);
        
        foreach ($request->order as $position => $id) {
            WhatsAppRotationMessage::where('id', $id)->update(['order' => $position + 1]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully!'
        ]);
    }
    
    /**
     * Get message data for editing (AJAX)
     */
    public function edit($id)
    {
        $message = WhatsAppRotationMessage::findOrFail($id);
        return response()->json($message);
    }
}
