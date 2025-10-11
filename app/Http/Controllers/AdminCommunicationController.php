<?php

namespace App\Http\Controllers;

use App\Models\CommunicationProduct;
use App\Models\CommunicationOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCommunicationController extends Controller
{
    // =====================================
    // GESTION DES PRODUITS
    // =====================================

    /**
     * Afficher la liste des produits
     */
    public function products()
    {
        $products = CommunicationProduct::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.communication.products.index', compact('products'));
    }

    /**
     * Afficher le formulaire de création d'un produit
     */
    public function createProduct()
    {
        return view('admin.communication.products.create');
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reference' => 'required|string|max:100|unique:communication_products,reference',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('communication/products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $product = CommunicationProduct::create($validated);

        return redirect()
            ->route('admin.communication.products')
            ->with('success', 'Produit créé avec succès');
    }

    /**
     * Afficher le formulaire d'édition d'un produit
     */
    public function editProduct(CommunicationProduct $product)
    {
        return view('admin.communication.products.edit', compact('product'));
    }

    /**
     * Mettre à jour un produit
     */
    public function updateProduct(Request $request, CommunicationProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reference' => 'required|string|max:100|unique:communication_products,reference,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Gestion de l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $imagePath = $request->file('image')->store('communication/products', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()
            ->route('admin.communication.products')
            ->with('success', 'Produit mis à jour avec succès');
    }

    /**
     * Supprimer un produit
     */
    public function destroyProduct(CommunicationProduct $product)
    {
        // Vérifier si le produit a des commandes associées
        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.communication.products')
                ->with('error', 'Impossible de supprimer ce produit car il est utilisé dans des commandes');
        }

        // Supprimer l'image si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.communication.products')
            ->with('success', 'Produit supprimé avec succès');
    }

    // =====================================
    // GESTION DES COMMANDES
    // =====================================

    /**
     * Afficher la liste des commandes
     */
    public function orders(Request $request)
    {
        $query = CommunicationOrder::with(['user', 'items.product'])
            ->orderBy('ordered_at', 'desc');

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrer par période
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('ordered_at', today());
                    break;
                case 'week':
                    $query->whereBetween('ordered_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('ordered_at', now()->month)
                          ->whereYear('ordered_at', now()->year);
                    break;
            }
        }

        $orders = $query->paginate(20);

        // Statistiques
        $stats = [
            'total_orders' => CommunicationOrder::count(),
            'pending_orders' => CommunicationOrder::where('status', 'en_attente')->count(),
            'total_amount_month' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                ->whereYear('ordered_at', now()->year)
                ->sum('total_amount'),
            'orders_this_month' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                ->whereYear('ordered_at', now()->year)
                ->count(),
        ];

        return view('admin.communication.orders.index', compact('orders', 'stats'));
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateOrderStatus(Request $request, CommunicationOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:en_attente,validee,en_preparation,expediee,livree,annulee',
            'notes' => 'nullable|string|max:1000'
        ]);

        $order->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $order->notes
        ]);

        // Log de l'activité (optionnel - à implémenter si nécessaire)
        // ActivityLog::create([...]);

        return redirect()
            ->back()
            ->with('success', 'Statut de la commande mis à jour');
    }

    /**
     * Afficher les détails d'une commande (optionnel)
     */
    public function showOrder(CommunicationOrder $order)
    {
        $order->load(['user', 'items.product']);
        
        return view('admin.communication.orders.show', compact('order'));
    }



    /**
     * Obtenir les statistiques globales (pour le dashboard)
     */
    public function getStats()
    {
        return [
            'total_products' => CommunicationProduct::count(),
            'active_products' => CommunicationProduct::active()->count(),
            'out_of_stock' => CommunicationProduct::where('stock_quantity', 0)->count(),
            'total_orders' => CommunicationOrder::count(),
            'pending_orders' => CommunicationOrder::where('status', 'en_attente')->count(),
            'orders_this_month' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                ->whereYear('ordered_at', now()->year)
                ->count(),
            'revenue_this_month' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                ->whereYear('ordered_at', now()->year)
                ->sum('total_amount'),
        ];
    }

    /**
     * Gérer les alertes de stock faible
     */
    public function lowStockAlert()
    {
        $lowStockProducts = CommunicationProduct::where('is_active', true)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return view('admin.communication.products.low-stock', compact('lowStockProducts'));
    }
}