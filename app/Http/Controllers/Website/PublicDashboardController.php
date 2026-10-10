<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CivilService;
use App\Models\DocumentUpload;
use App\Models\OtherService;
use App\Models\Setting;
use App\Models\StatusUpdate;
use Illuminate\Support\Facades\DB;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $featureEnabled = Setting::getBoolean('feature.public_dashboard', true);

        if (!$featureEnabled) {
            return view('website.public-dashboard', [
                'featureEnabled' => false,
            ]);
        }

        $civilSummary = $this->buildStatusSummary(CivilService::class, CivilService::statusFlow());
        $otherSummary = $this->buildStatusSummary(OtherService::class, OtherService::statusFlow());
        $totalSummary = $this->mergeSummaries($civilSummary, $otherSummary);

        $documentSummary = $this->buildDocumentSummary();
        $recentDocuments = $this->recentDocuments();
        $recentUpdates = $this->recentStatusUpdates();

        $totalRequests = array_sum($totalSummary);
        $completed = $totalSummary['registered'] ?? 0;
        $inProcess = $this->calculateInProcessCount($totalSummary);
        $completionRate = $totalRequests > 0 ? round(($completed / $totalRequests) * 100, 1) : 0;

        $milestones = $this->buildMilestones($totalSummary);

        $statusLabels = $this->statusLabels();

        return view('website.public-dashboard', [
            'featureEnabled' => true,
            'statusSummary' => [
                'civil' => $civilSummary,
                'other' => $otherSummary,
                'total' => $totalSummary,
            ],
            'statusLabels' => $statusLabels,
            'documentSummary' => $documentSummary,
            'recentDocuments' => $recentDocuments,
            'recentUpdates' => $recentUpdates,
            'milestones' => $milestones,
            'completionRate' => $completionRate,
            'totals' => [
                'requests' => $totalRequests,
                'completed' => $completed,
                'in_process' => $inProcess,
            ],
        ]);
    }

    private function buildStatusSummary(string $modelClass, array $flow): array
    {
        $base = array_fill_keys($flow, 0);

        $counts = $modelClass::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        foreach ($counts as $status => $total) {
            $base[$status] = (int) $total;
        }

        // Include unexpected statuses so they are not lost
        foreach ($counts as $status => $total) {
            if (!array_key_exists($status, $base)) {
                $base[$status] = (int) $total;
            }
        }

        return $base;
    }

    private function mergeSummaries(array $civil, array $other): array
    {
        $keys = array_unique(array_merge(array_keys($civil), array_keys($other)));

        $merged = [];
        foreach ($keys as $key) {
            $merged[$key] = ($civil[$key] ?? 0) + ($other[$key] ?? 0);
        }

        return $merged;
    }

    private function buildDocumentSummary(): array
    {
        $base = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        $counts = DocumentUpload::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        foreach ($counts as $status => $total) {
            $base[$status] = (int) $total;
        }

        $base['total'] = array_sum($base);

        return $base;
    }

    private function recentDocuments()
    {
        return DocumentUpload::with('civilService')
            ->latest()
            ->limit(4)
            ->get();
    }

    private function recentStatusUpdates()
    {
        return StatusUpdate::with('statusable')
            ->latest('status_date')
            ->latest()
            ->limit(6)
            ->get();
    }

    private function calculateInProcessCount(array $summary): int
    {
        $inProcessStatuses = [
            'accepted',
            'psa_processing',
            'posting',
            'ready_for_approval',
        ];

        $count = 0;
        foreach ($inProcessStatuses as $status) {
            $count += $summary[$status] ?? 0;
        }

        return $count;
    }

    private function buildMilestones(array $summary): array
    {
        return [
            [
                'label' => 'Intake',
                'description' => 'Requests received today',
                'count' => ($summary['submitted'] ?? 0) + ($summary['inquiry'] ?? 0) + ($summary['to_be_encoded_brap'] ?? 0),
            ],
            [
                'label' => 'Requirements',
                'description' => 'Checklist + validation',
                'count' => ($summary['checklist_given'] ?? 0) + ($summary['incomplete_requirements'] ?? 0),
            ],
            [
                'label' => 'Processing',
                'description' => 'Accepted & PSA coordination',
                'count' => ($summary['accepted'] ?? 0) + ($summary['psa_processing'] ?? 0),
            ],
            [
                'label' => 'Ready / Released',
                'description' => 'Documents for release',
                'count' => ($summary['ready_for_approval'] ?? 0) + ($summary['registered'] ?? 0),
            ],
        ];
    }

    private function statusLabels(): array
    {
        return [
            'submitted' => 'Submitted',
            'inquiry' => 'Inquiry',
            'to_be_encoded_brap' => 'BRAP Encoding',
            'checklist_given' => 'Checklist Given',
            'incomplete_requirements' => 'Incomplete Requirements',
            'accepted' => 'Accepted',
            'psa_processing' => 'PSA Processing',
            'posting' => 'For Posting',
            'ready_for_approval' => 'Ready for Approval',
            'registered' => 'Registered / Released',
        ];
    }
}

