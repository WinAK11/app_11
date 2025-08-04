<?php
namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FreeProductController extends Controller {
    public function index() {
        $ebooks = Ebook::orderBy( 'id', 'ASC' )->paginate( 10 );
        // dd( $ebooks );
        return view( 'free-products', compact( 'ebooks' ) );
    }

    public function ebooks() {
        $ebooks = Ebook::orderBy( 'id', 'ASC' )->paginate( 10 );
        // dd( $ebooks );
        return view( 'admin.ebooks', compact( 'ebooks' ) );
    }

    public function ebook_add() {
        $authors = Author::all();
        $categories = Category::select( 'id', 'name' )->orderBy( 'name' )->get();
        return view( 'admin.ebook-add', compact( 'authors', 'categories' ) );
    }

    public function ebook_store( Request $request ) {
        $validatedData = $request->validate( [
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:epub,pdf',
            'cover_image_data' => 'nullable|string' // base64 cover
        ] );

        $format = strtolower( $request->file( 'file' )->getClientOriginalExtension() );

        // Sanitize folder name from title
        $folderName = Str::slug( $validatedData[ 'title' ] );
        $basePath = public_path( "uploads/ebooks/$folderName" );

        // Create folder if missing
        if ( !file_exists( $basePath ) ) {
            mkdir( $basePath, 0755, true );
        }

        // Move EPUB file
        $ebookName = time() . '_' . $request->file( 'file' )->getClientOriginalName();
        $request->file( 'file' )->move( $basePath, $ebookName );
        $filePath = "uploads/ebooks/$folderName/$ebookName";

        // Decode and save cover image
        $coverPath = null;
        if ( $request->filled( 'cover_image_data' ) ) {
            $base64 = $request->cover_image_data;
            $imageData = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $base64 ) );
            $coverName = time() . '_cover.jpg';
            $coverFullPath = "$basePath/$coverName";
            file_put_contents( $coverFullPath, $imageData );
            $coverPath = "uploads/ebooks/$folderName/$coverName";
        }

        // Save to database
        $ebook = Ebook::create( [
            'title' => $validatedData[ 'title' ],
            'author' => $validatedData[ 'author_name' ],
            'category_id' => $validatedData[ 'category_id' ],
            'description' => $validatedData[ 'description' ] ?? '',
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'format' => $format
        ] );

        Log::info( 'Ebook created', [ 'id' => $ebook->id ] );

        // return response()->json( [
        //     'message' => 'Ebook uploaded successfully!',
        //     'ebook' => $ebook
        // ] );
        return redirect()->route( 'admin.ebooks' )->with( 'status', 'Ebook has been added successfully.' );
    }

    public function ebook_edit( $id ) {
        $ebook = Ebook::findOrFail( $id );
        return view( 'admin.ebook-edit', compact( 'ebook' ) );
    }

    public function ebook_read( $id ) {
        $ebook = Ebook::findOrFail( $id );

        return view( 'epub-reader', compact( 'ebook' ) );
    }

    // public function ebook_update( Request $request ) {
    //     $validatedData = $request->validate( [
    //         'ebook_id' => 'required|integer|exists:ebooks,id',
    //         'title' => 'required|string|max:255',
    //         'author_name' => 'required|string|max:255',
    //         'category' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'file' => 'nullable|file|mimes:epub,pdf',
    //         'cover_image_data' => 'nullable|string',
    // ] );

    //     $ebook = Ebook::findOrFail( $validatedData[ 'ebook_id' ] );

    //     $folderName = Str::slug( $validatedData[ 'title' ] );
    //     $basePath = public_path( "uploads/ebooks/$folderName" );

    //     dd( $ebook, $folderName, $basePath, $request );

    //     if ( !file_exists( $basePath ) ) {
    //         mkdir( $basePath, 0755, true );
    //     }

    //     // Handle file upload
    //     if ( $request->hasFile( 'file' ) ) {
    //         if ( $ebook->file_path && file_exists( public_path( $ebook->file_path ) ) ) {
    //             unlink( public_path( $ebook->file_path ) );
    //         }

    //         $ebookName = time() . '_' . $request->file( 'file' )->getClientOriginalName();
    //         $request->file( 'file' )->move( $basePath, $ebookName );

    //         $ebook->file_path = "uploads/ebooks/$folderName/$ebookName";
    //         $ebook->format = strtolower( $request->file( 'file' )->getClientOriginalExtension() );
    //     }

    //     // Handle cover upload
    //     if ( $request->filled( 'cover_image_data' ) ) {
    //         if ( $ebook->cover_path && file_exists( public_path( $ebook->cover_path ) ) ) {
    //             unlink( public_path( $ebook->cover_path ) );
    //         }

    //         $imageData = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $request->cover_image_data ) );
    //         $coverName = time() . '_cover.jpg';
    //         file_put_contents( "$basePath/$coverName", $imageData );

    //         $ebook->cover_path = "uploads/ebooks/$folderName/$coverName";
    //     }

    //     // Final metadata update
    //     $ebook->title = $validatedData[ 'title' ];
    //     $ebook->author = $validatedData[ 'author_name' ];
    //     $ebook->category = $validatedData[ 'category' ];
    //     $ebook->description = $validatedData[ 'description' ] ?? '';

    //     // Ensure file_path still exists
    //     if ( !$ebook->file_path ) {
    //         return back()->withErrors( [ 'file' => 'Ebook file is required.' ] );
    //     }

    //     $ebook->save();

    //     return redirect()->route( 'admin.ebooks' )->with( 'status', 'Ebook updated successfully!' );
    // }

    public function ebook_delete( $id ) {
        $ebook = Ebook::findOrFail( $id );

        if ( $ebook->file_path && file_exists( public_path( $ebook->file_path ) ) ) {
            unlink( public_path( $ebook->file_path ) );
        }

        if ( $ebook->cover_path && file_exists( public_path( $ebook->cover_path ) ) ) {
            unlink( public_path( $ebook->cover_path ) );
        }

        $ebook->delete();
        return back()->with( 'status', 'Ebook deleted!' );

    }
}
