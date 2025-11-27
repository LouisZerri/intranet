<?php

namespace App\Http\Controllers;

use App\Models\CommunicationProduct;
use App\Models\CommunicationOrder;
use App\Models\CommunicationOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class CommunicationController extends Controller
{
    /**
     * Afficher le catalogue de produits
     */
    public function index()
    {
        $products = CommunicationProduct::active()
            ->orderBy('name')
            ->paginate(12);

        return view('communication.index', compact('products'));
    }

    /**
     * Afficher les détails d'un produit
     */
    public function show(CommunicationProduct $product)
    {
        return view('communication.show', compact('product'));
    }

    /**
     * Afficher le panier
     */
    public function cart()
    {
        $cart = session()->get('communication_cart', []);
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = CommunicationProduct::find($productId);
            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('communication.cart', compact('products', 'total'));
    }

    /**
     * Ajouter au panier
     */
    public function addToCart(Request $request, CommunicationProduct $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('communication_cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id] += $validated['quantity'];
        } else {
            $cart[$product->id] = $validated['quantity'];
        }

        session()->put('communication_cart', $cart);

        return back()->with('success', 'Produit ajouté au panier !');
    }

    /**
     * Mettre à jour le panier
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);

        $cart = [];
        foreach ($validated['quantities'] as $productId => $quantity) {
            if ($quantity > 0) {
                $cart[$productId] = $quantity;
            }
        }

        session()->put('communication_cart', $cart);

        return back()->with('success', 'Panier mis à jour !');
    }

    /**
     * Vider le panier
     */
    public function clearCart()
    {
        session()->forget('communication_cart');
        return back()->with('success', 'Panier vidé !');
    }

    /**
     * Passer la commande
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('communication_cart', []);

        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Votre panier est vide.']);
        }

        DB::beginTransaction();
        try {
            // Créer la commande
            $order = CommunicationOrder::create([
                'user_id' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'en_attente',
            ]);

            // Ajouter les items
            foreach ($cart as $productId => $quantity) {
                $product = CommunicationProduct::findOrFail($productId);
                
                CommunicationOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price ?? 0,
                ]);
            }

            // Calculer le total
            $order->calculateTotal();

            // Envoyer l'email
            $this->sendOrderEmail($order);

            // Vider le panier
            session()->forget('communication_cart');

            DB::commit();

            return redirect()->route('communication.order-success', $order)
                ->with('success', 'Votre commande a été envoyée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la commande.']);
        }
    }

    /**
     * Page de succès
     */
    public function orderSuccess(CommunicationOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('communication.order-success', compact('order'));
    }

    /**
     * Mes commandes
     */
    public function myOrders()
    {
        $orders = CommunicationOrder::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('communication.my-orders', compact('orders'));
    }

    /**
     * Détails d'une commande
     */
    public function orderDetails(CommunicationOrder $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($order->user_id !== Auth::id() && !$user->isAdministrateur()) {
            abort(403);
        }

        $order->load(['items.product', 'user']);

        return view('communication.order-details', compact('order'));
    }

    /**
     * Envoyer l'email de commande
     */
    private function sendOrderEmail(CommunicationOrder $order)
    {
        $order->load(['items.product', 'user']);

        Mail::send('emails.communication-order', ['order' => $order], function ($message) use ($order) {
            $message->to('gestimmo.presta@gmail.com')
                ->subject('Nouvelle commande de communication - ' . $order->order_number);
        });
    }
}