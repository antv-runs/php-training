<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int User ID who requested the export
     */
    public $userId;

    /**
     * @var array Filter parameters
     */
    public $filters;

    /**
     * @var string Export format: 'csv' or 'excel'
     */
    public $format;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param array $filters
     * @param string $format
     */
    public function __construct(int $userId, array $filters = [], string $format = 'csv')
    {
        $this->userId = $userId;
        $this->filters = $filters;
        $this->format = $format;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Build the query with filters
            $query = Product::with('category');

            // Apply filters
            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            if (!empty($this->filters['category_id'])) {
                $query->where('category_id', $this->filters['category_id']);
            }

            // Handle status filter
            $status = $this->filters['status'] ?? 'active';
            if ($status === 'deleted') {
                $query = Product::onlyTrashed();
            } elseif ($status === 'all') {
                $query = Product::withTrashed();
            }

            // Get all matching products
            $products = $query->latest('id')->get();

            // Generate file
            $filename = $this->generateFileName();
            $filePath = $this->exportToFile($products, $filename);

            // Save file info to database or storage metadata
            $fileUrl = Storage::disk('public')->url($filePath);

            // Get user and send notification
            $user = User::find($this->userId);
            if ($user && $user->email) {
                // Send email with download link
                Mail::send('emails.export_ready', [
                    'user' => $user,
                    'downloadUrl' => $fileUrl,
                    'filename' => $filename,
                    'format' => strtoupper($this->format),
                    'productCount' => $products->count(),
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Your Product Export is Ready');
                });
            }

            // Log success
            \Log::info("Product export completed", [
                'user_id' => $this->userId,
                'file' => $filename,
                'count' => $products->count(),
                'format' => $this->format,
            ]);

        } catch (\Exception $e) {
            \Log::error("Product export failed", [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);

            // Notify user of failure
            $user = User::find($this->userId);
            if ($user && $user->email) {
                Mail::send('emails.export_failed', [
                    'user' => $user,
                    'error' => $e->getMessage(),
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Product Export Failed');
                });
            }

            throw $e;
        }
    }

    /**
     * Export products to CSV or Excel file
     *
     * @param mixed $products
     * @param string $filename
     * @return string File path in storage
     */
    protected function exportToFile($products, string $filename): string
    {
        $filePath = "exports/{$filename}";
        $directory = "public/exports";

        // Ensure directory exists
        if (!Storage::disk('public')->exists('exports')) {
            Storage::disk('public')->makeDirectory('exports');
        }

        if ($this->format === 'excel') {
            return $this->exportToExcel($products, $filePath);
        }

        return $this->exportToCsv($products, $filePath);
    }

    /**
     * Export products to CSV file
     *
     * @param mixed $products
     * @param string $filePath
     * @return string
     */
    protected function exportToCsv($products, string $filePath): string
    {
        $file = fopen('php://temp', 'r+');

        // Write headers
        $headers = ['ID', 'Name', 'Slug', 'Price', 'Category', 'Description', 'Image', 'Created At'];
        fputcsv($file, $headers);

        // Write data
        foreach ($products as $product) {
            fputcsv($file, [
                $product->id,
                $product->name,
                $product->slug,
                $product->price,
                $product->category?->name ?? 'N/A',
                $product->description,
                $product->image ? asset("storage/{$product->image}") : 'N/A',
                $product->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Reset cursor to beginning
        rewind($file);

        // Get content and close temporary file
        $content = stream_get_contents($file);
        fclose($file);

        // Store in public disk
        Storage::disk('public')->put($filePath, $content);

        return $filePath;
    }

    /**
     * Export products to Excel file
     * For basic implementation, we use CSV format
     * To use true Excel, install maatwebsite/excel package
     *
     * @param mixed $products
     * @param string $filePath
     * @return string
     */
    protected function exportToExcel($products, string $filePath): string
    {
        // Change extension to xlsx
        $filePath = str_replace('.csv', '.xlsx', $filePath);

        // For now, create CSV and change extension
        // If you want true Excel support, uncomment the code below after installing maatwebsite/excel
        // $export = new ProductsExport($products);
        // Excel::store($export, $filePath, 'public');

        // Fallback to CSV
        $csvPath = str_replace('.xlsx', '.csv', $filePath);
        $this->exportToCsv($products, $csvPath);

        // Return xlsx path (user can use CSV as is)
        return $csvPath;
    }

    /**
     * Generate unique filename with timestamp
     */
    protected function generateFileName(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = $this->format === 'excel' ? 'xlsx' : 'csv';

        return "products_export_{$timestamp}.{$extension}";
    }
}
