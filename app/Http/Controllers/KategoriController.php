<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    public function index() {
        // $data = [
        //     'kategore_kode' => 'SNK',
        //     'kategori_nama' => 'Snack/Makanan Ringan',
        //     'created_at' => now(),
        // ];
        // DB::table('m_kategori')->insert($data);
        // return 'Insert data baru berhasil';

        // $row= DB::table('m_kategori')->where('kategore_kode', 'SNK')->update(['kategori_nama' => 'Camilan']);
        // return 'Update data berhasil. Jumlah data yang disimpan '.$row. ' baris';

        // $row = DB::table('m_kategori')->where('kategore_kode', 'SNK')->delete();
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row. ' baris';

        // 

        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activateMenu = 'kategori';

        return view('kategori.index', compact('breadcrumb', 'page', 'activemenu'));

        $data = DB::table('m_kategori')->get();
        return view('kategori', ['data' => $data]);
    }

    public function list(Request $request) {
        $kategori = KategoriModel::select('kategori_id', 'kategore_kode', 'kategori_nama');

        if ($request->kategori_nama) {
            $kategori->where('kategori_nama', 'like', '%' . $request->kategori_nama . '%');
        }

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                 $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                    . csrf_field() . method_field('DELETE') . 
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';

                    return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah kategori Baru'
        ];

        $activeMenu = 'kategori';

        return view('ketegori.create', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function store(Request $request) {
        $request->validate([
            'kategore_kode' => 'required|unique:m_kategori',
            'kategori_nama' => 'required'
        ]);

        KategoriModel::create($request->all());

        return redirect('/kategori')->with('success', 'Data user berhasil ditambahkan');
    }

    public function edit($id) {
        $breadcrumb = (object) [
        'title' => 'Edit Ketgori',
        'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Kategori'
        ];

        $activateMenu = 'kategori';

        $kategori = KategoriModel::findOrFail($id);

        return view('kategori.edit', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'kategori_kode' => 'required|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required'
        ]);

        KategoriModel::findorFail($id)->update($request->all());

        return redirect('/kategori')->with('status', 'Data kategori berhasil diubah');
    }

    public function destroy($id)
    {
        $kategori = KategoriModel::find($id);
    
        if (!$kategori) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }
    
        try {
            KategoriModel::destroy($id);
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (\Exception $e) {
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
    
}
