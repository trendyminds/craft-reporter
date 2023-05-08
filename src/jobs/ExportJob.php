<?php

namespace trendyminds\reporter\jobs;

use Craft;
use craft\elements\Asset;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use craft\queue\BaseJob;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use trendyminds\reporter\Reporter;

class ExportJob extends BaseJob
{
	public $name;
	public $handle;

	public function execute($queue): void
	{
		$report = Reporter::getInstance()->getReportData($this->handle);
		$batch = $report->query->batch();

		// The total number of records to process is either the limit set by the user, or all records in the query
		$totalRecords = $report->query->limit ?? $report->query->count();

		/**
		 * Construct the temporary CSV file for writing each row
		 */
		$uploadDirectory = Craft::$app->getPath()->getTempAssetUploadsPath();
		$fileName = $this->handle . "-" . StringHelper::UUID() . '.csv';
		$filePath = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;

		/**
		 * Use the headers provided by the user OR
		 * Get the first row of the query, transform the data, and output it as the first
		 * row in our CSV to act as the headers
		 */

		if ($report->headers) {
			$headers = $report->headers;
		} else {
			$resource = new Collection([ $report->query->one() ], $report->transformer);
			$record = (new Manager())->createData($resource)->toArray()['data'][0];
			$headers = array_keys($record);
		}

		// Create the first header row
		$output = fopen($filePath, "w+");

		//header utf-8
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		fputcsv($output, $headers());

		// Loop through each element and insert it into the CSV as a new row
		foreach ($batch as $i => $row) {
			$step = ($i + 1) * $batch->batchSize;

			$resource = new Collection($row, $report->transformer);
			$data = (new Manager())->createData($resource)->toArray()['data'];

			// Collect and filter out any empty arrays (in case the user is returning an empty array to skip over it)
			$data = collect($data)->filter()->values()->toArray();

			foreach ($data as $record) {
				//header utf-8
				fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

				fputcsv($output, $record);
			}

			// Update the progress of the report to indicate something is happening
			$this->setProgress(
				$queue,
				($step / $totalRecords),
				"Exporting $step of $totalRecords"
			);
		}

		fseek($output, 0);

		/**
		 * Create a new asset element using the file.
		 */
		try {
			$folderVolume = Reporter::getInstance()->getExportPath();

			$asset = new Asset();
			$asset->tempFilePath = $filePath;
			$asset->title = $this->name;
			$asset->filename = $fileName;
			$asset->kind = "application/csv";
			$asset->newFolderId = (int) $folderVolume->id;

			// Only attempt to attach an author if we can determine the identity of the signed-in user
			// This ensures we can run this without error from the console commands
			if (Craft::$app->user->identity) {
				$asset->uploaderId = Craft::$app->user->identity->id;
			}

			$success = Craft::$app->elements->saveElement($asset);

			if ($success) {
				FileHelper::unlink($filePath);
			}
		} catch(\Exception $e) {
			throw new \Exception($e);
		}
	}

	protected function defaultDescription(): string
	{
		return "Running \"{$this->name}\" report";
	}
}
