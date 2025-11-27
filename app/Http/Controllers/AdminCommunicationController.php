<?php

namespace App\Http\Controllers;

use App\Models\CommunicationProduct;
use App\Models\CommunicationOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCommunicationController extends Controller
{
    /**
     * Liste paginée des produits de communication
     */
    public function products()
    {
        $products = CommunicationProduct::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.communication.products.index', compact('products'));
    }

    /**
     * Formulaire de création de produit
     */
    public function createProduct()
    {
        return view('admin.communication.products.create');
    }

    /**
     * Création d'un nouveau produit (avec gestion d'image)
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

        // Upload image si présente
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('communication/products', 'public');
            $validated['image'] = $imagePath;
        }

        // Checkbox HTML
        $validated['is_active'] = $request->has('is_active');

        CommunicationProduct::create($validated);

        return redirect()
            ->route('admin.communication.products')
            ->with('success', 'Produit créé avec succès');
    }

    /**
     * Formulaire d'édition de produit
     */
    public function editProduct(CommunicationProduct $product)
    {
        return view('admin.communication.products.edit', compact('product'));
    }

    /**
     * Mise à jour d'un produit (et image si modifiée)
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

        if ($request->hasFile('image')) {
            // Suppression de l'ancienne image
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
     * Suppression d'un produit (interdit si utilisé dans des commandes)
     */
    public function destroyProduct(CommunicationProduct $product)
    {
        // Refuse suppression si produit présent dans des commandes
        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.communication.products')
                ->with('error', 'Impossible de supprimer ce produit car il est utilisé dans des commandes');
        }

        // Suppression image disque si présente
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.communication.products')
            ->with('success', 'Produit supprimé avec succès');
    }

    /**
     * Liste paginée des commandes avec filtres basiques
     */
    public function orders(Request $request)
    {
        $query = CommunicationOrder::with(['user', 'items.product'])
            ->orderBy('ordered_at', 'desc');

        // Filtres simples (statut & période)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
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

        // Stats globales pour panneau admin
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
     * Changement du statut d'une commande (valide, expédié etc)
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

        return redirect()
            ->back()
            ->with('success', 'Statut de la commande mis à jour');
    }

    /**
     * Détail d'une commande
     */
    public function showOrder(CommunicationOrder $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.communication.orders.show', compact('order'));
    }

    /**
     * Statistiques globales pour dashboard
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
     * Liste produits en stock faible (<=10)
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