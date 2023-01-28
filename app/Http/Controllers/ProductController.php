<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $kategori = Category::all()->pluck('nama_kategori','id_kategori');
        return view('product.index', compact('kategori'));
    }

    public function data()
    {
        $product = Product::leftJoin('categories', 'categories.id_kategori', 'products.id_kategori')
                ->select('products.*', 'nama_kategori')
                ->orderBy('kode_produk', 'asc')
                ->get();

        return datatables()
            ->of($product)
            ->addIndexColumn()
            ->addColumn('select_all', function($product) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $product->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function($product) {
                return '<span class="label label-success">'. $product->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($product) {
                return format_uang($product->harga_beli);
            })
            ->addColumn('harga_jual', function ($product) {
                return format_uang($product->harga_jual);
            })
            ->addColumn('stok', function ($product) {
                return format_uang($product->stok);
            })
            ->addColumn('aksi', function ($product) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('product.update', $product->id_produk) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('product.destroy', $product->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produk = Product::latest()->first() ?? new Product();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);

        $produk = Product::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Product::find($id);

        return response()->json($produk);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Product::find($id);
        $produk->update($request->all());

        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Product::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Product::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }
    
    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Product::find($id);
            $dataproduk[] = $produk;
        }

        // nomor bantuan untuk pengecekaan table row pada view
        $no = 1;

        $pdf = PDF::loadView('product.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('product.pdf');
    }
}
