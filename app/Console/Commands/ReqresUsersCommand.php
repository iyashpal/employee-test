<?php

namespace App\Console\Commands;

use App\Facades\Reqres;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ReqresUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reqres:sync-users {--fetch-all : Fetch all records pages records.} {--per-page-records=10 : The number of records to pull per page from ReqRes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull reqres users and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $page = 1;
        $data = collect();
        $fetchAll = $this->option('fetch-all');
        $perPageRecords = $this->option('per-page-records');

        $this->info('Fetching users from ReqRes "reqres.in"');

        try {
            $response = Reqres::getUsers(page: $page, perPage: $perPageRecords);

            // Merge response data to the output data collection.
            $data->push(...($response->ok() ? $response->json('data') : []));

            // Generate pages range based on response total pages
            collect(range($page, $response->json('total_pages')))

                // Filter pages which are not fetched and "--fetch-all" option is true
                ->filter(fn($targetPage) => $targetPage !== $page && $fetchAll)

                // Fetch records for the filtered pages.
                ->each(function ($page) use ($data, $perPageRecords) {
                    $response = Reqres::getUsers(page: $page, perPage: $perPageRecords);

                    // Merge response data to the output data collection.
                    $data->push(...($response->ok() ? $response->json('data') : []));
                });

            $this->info("Total records fetched: {$data->count()}");
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 0;
        }

        $this->generateOutputTable($data);

        return 1;
    }

    /**
     * Generate output table for fetched users.
     *
     * @param Collection $data
     * @return void
     */
    protected function generateOutputTable(Collection $data): void
    {
        $this->table(
            ['ID', 'First Name', 'Last Name', 'Email', 'Avatar'],
            $data->map(fn($item) => [
                'id' => $item['id'],
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'],
                'email' => $item['email'],
                'avatar' => $item['avatar'],
            ])->toArray(),
        );
    }
}
