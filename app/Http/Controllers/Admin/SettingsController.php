<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function processFlow()
    {
        // Load from database, with fallback to default data
        $brapFlow = $this->getWorkflowSteps('brap', $this->getDefaultBrapFlow());
        $regularFlow = $this->getWorkflowSteps('regular', $this->getDefaultRegularFlow());
        $kioskFlow = $this->getWorkflowSteps('kiosk', $this->getDefaultKioskFlow());
        $approvalFlow = $this->getWorkflowSteps('approval', $this->getDefaultApprovalFlow());

        // Get workflow steps with IDs for edit/delete functionality
        $brapSteps = WorkflowStep::where('workflow_type', 'brap')->orderBy('display_order')->get();
        $regularSteps = WorkflowStep::where('workflow_type', 'regular')->orderBy('display_order')->get();
        $kioskSteps = WorkflowStep::where('workflow_type', 'kiosk')->orderBy('display_order')->get();
        $approvalSteps = WorkflowStep::where('workflow_type', 'approval')->orderBy('display_order')->get();

        return view('admin.process-flow', compact(
            'brapFlow', 'regularFlow', 'kioskFlow', 'approvalFlow',
            'brapSteps', 'regularSteps', 'kioskSteps', 'approvalSteps'
        ));
    }

    /**
     * Get workflow steps from database, or return default if empty
     */
    private function getWorkflowSteps($type, $defaultData)
    {
        $steps = WorkflowStep::getByType($type);
        
        // If database is empty, seed it with default data
        if (empty($steps)) {
            $this->seedWorkflowSteps($type, $defaultData);
            return WorkflowStep::getByType($type);
        }
        
        return $steps;
    }

    /**
     * Seed workflow steps into database
     */
    private function seedWorkflowSteps($type, $data)
    {
        foreach ($data as $index => $step) {
            $title = $step['status'] ?? $step['step'] ?? '';
            WorkflowStep::create([
                'workflow_type' => $type,
                'title' => $title,
                'description' => $step['description'],
                'display_order' => $index,
            ]);
        }
    }

    /**
     * Get default BRAP workflow data
     */
    private function getDefaultBrapFlow()
    {
        return [
            ['status' => 'Submitted', 'description' => 'Initial request submitted via kiosk or online. System generates tracking number (C2S-YYYYMMDD-XXXX).'],
            ['status' => 'Inquiry', 'description' => 'Request is reviewed by officer. Officer verifies request details.'],
            ['status' => 'To Be Encoded BRAP', 'description' => 'Request is prepared for BRAP encoding. Officer prepares documents for BRAP submission.'],
            ['status' => 'Checklist Given', 'description' => 'Officer provides checklist of required documents. System sends SMS to client with checklist items.'],
            ['status' => 'Incomplete Requirements', 'description' => 'Client needs to submit additional documents. (Can be skipped if requirements are complete)'],
            ['status' => 'Accepted', 'description' => 'All requirements are complete and request is accepted. Officer marks request as accepted.'],
            ['status' => 'PSA Processing', 'description' => 'Request is being processed by PSA. CRO coordinates with PSA. May take several days.'],
            ['status' => 'Posting', 'description' => 'Request is posted for public viewing (10 business days). System tracks posting start date.'],
            ['status' => 'Ready for Approval', 'description' => 'Request has completed posting period and is ready for approval. Appears in approval requests list.'],
            ['status' => 'Registered', 'description' => 'Request is complete and certificate is ready. Certificate is ready for pickup.'],
        ];
    }

    /**
     * Get default Regular workflow data
     */
    private function getDefaultRegularFlow()
    {
        return [
            ['status' => 'Submitted', 'description' => 'Initial request submitted via kiosk or online. System generates tracking number (C2S-YYYYMMDD-XXXX).'],
            ['status' => 'Inquiry', 'description' => 'Request is reviewed by officer. Officer verifies request details.'],
            ['status' => 'Checklist Given', 'description' => 'Officer provides checklist of required documents. System sends SMS to client with checklist items.'],
            ['status' => 'Incomplete Requirements', 'description' => 'Client needs to submit additional documents. (Can be skipped if requirements are complete)'],
            ['status' => 'Accepted', 'description' => 'All requirements are complete and request is accepted. Officer marks request as accepted.'],
            ['status' => 'Registered', 'description' => 'Request is complete and certificate is ready. Certificate is ready for pickup.'],
        ];
    }

    /**
     * Get default Kiosk workflow data
     */
    private function getDefaultKioskFlow()
    {
        return [
            ['step' => 'Kiosk Startup', 'description' => 'User accesses the kiosk home screen. Options: Civil Registry Services, Other Services, Track Request, Municipal Information, Wayfinding.'],
            ['step' => 'Select Service Category', 'description' => 'User selects service category (Civil Registry or Other Services). System displays available service types.'],
            ['step' => 'Select Service Type', 'description' => 'User selects specific service type (Birth Certificate, Marriage Certificate, CENOMAR, Death Certificate, etc.).'],
            ['step' => 'Fill Out Form', 'description' => 'User enters Full Name and Contact Number. Form validates input in real-time.'],
            ['step' => 'Submit Request', 'description' => 'User clicks "Submit & Print Receipt". System validates all required fields.'],
            ['step' => 'Generate Tracking Number', 'description' => 'System generates unique tracking number (C2S-YYYYMMDD-XXXX format). Ensures uniqueness.'],
            ['step' => 'Send SMS Inquiry', 'description' => 'System automatically sends SMS inquiry message to client\'s registered number.'],
            ['step' => 'Print Receipt', 'description' => 'System prints receipt with tracking number, name, service type, date, and time.'],
            ['step' => 'Confirmation Screen', 'description' => 'User sees success message and tracking number. Option to track request or return to home.'],
        ];
    }

    /**
     * Get default Approval workflow data
     */
    private function getDefaultApprovalFlow()
    {
        return [
            ['step' => 'Posting Status', 'description' => 'Request is posted for public viewing. Duration: 10 business days (excluding weekends and holidays).'],
            ['step' => 'Ready for Approval (Auto)', 'description' => 'System automatically changes status to "ready_for_approval" after 10 business days. Request appears in Approval Requests screen.'],
            ['step' => 'Approval Requests Screen', 'description' => 'Officer views list of requests ready for approval. Displays tracking number, client name, service type, and days in posting.'],
            ['step' => 'Review Request', 'description' => 'Officer reviews request details. Can view full request information and history.'],
            ['step' => 'Approve Request', 'description' => 'Officer clicks "Approve" button. System validates request is in "ready_for_approval" status.'],
            ['step' => 'Status: Registered', 'description' => 'Status changes to "registered". StatusUpdate record created with date and remarks.'],
            ['step' => 'SMS Notification', 'description' => 'System sends SMS notification to client. Message includes congratulations and pickup instructions.'],
        ];
    }

    /**
     * Get workflow step by ID
     */
    public function getWorkflowStep($id)
    {
        $step = WorkflowStep::findOrFail($id);
        return response()->json($step);
    }

    /**
     * Update workflow step
     */
    public function updateWorkflowStep(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $step = WorkflowStep::findOrFail($id);
        $step->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workflow step updated successfully',
            'step' => $step,
        ]);
    }

    /**
     * Create new workflow step
     */
    public function createWorkflowStep(Request $request)
    {
        $validated = $request->validate([
            'workflow_type' => 'required|in:brap,regular,kiosk,approval',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        // If display_order not provided, set it to the end
        if (!isset($validated['display_order'])) {
            $maxOrder = WorkflowStep::where('workflow_type', $validated['workflow_type'])
                ->max('display_order') ?? -1;
            $validated['display_order'] = $maxOrder + 1;
        }

        $step = WorkflowStep::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workflow step created successfully',
            'step' => $step,
        ]);
    }

    /**
     * Delete workflow step
     */
    public function deleteWorkflowStep($id)
    {
        $step = WorkflowStep::findOrFail($id);
        $step->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workflow step deleted successfully',
        ]);
    }
}