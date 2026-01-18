<?php

namespace App\Conversations;

use App\Models\BranchComplaint;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Modules\Base\Models\Branch;
use Modules\Support\Models\Complaint;

class BranchComplaintConversation extends Conversation {
    protected ?int $branchId = null;
    protected string $name;
    protected string $email;
    protected string $mobile;
    protected string $complaint;

    public function run(): void {
        $this->askBranch();
    }

    protected function askBranch(): void {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->say(__('chat.branches.none'));
            $this->bot->startConversation(new MainMenuConversation());

            return;
        }

        $buttons = [];
        foreach ($branches as $branch) {
            $buttons[] = Button::create($branch->name)->value((string) $branch->id);
        }

        $question = Question::create(__('chat.complaint.ask_branch'))
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if (!$answer->isInteractiveMessageReply()) {
                return $this->askBranch();
            }

            $this->branchId = (int) $answer->getValue();

            $this->askName();
        });
    }

    protected function askName(): void {
        $this->ask(__('chat.complaint.ask_name'), function (Answer $answer) {
            $this->name = trim($answer->getText());

            $this->askEmail();
        });
    }

    protected function askEmail(): void {
        $this->ask(__('chat.complaint.ask_email'), function (Answer $answer) {
            $email = trim($answer->getText());

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->say(__('chat.complaint.email_invalid'));

                return $this->askEmail();
            }

            $this->email = $email;

            $this->askMobile();
        });
    }

    protected function askMobile(): void {
        $this->ask(__('chat.complaint.ask_mobile'), function (Answer $answer) {
            $mobile = trim($answer->getText());

            if (!$this->isValidPhone($mobile)) {
                $this->say(__('chat.complaint.mobile_invalid'));

                return $this->askMobile();
            }

            $this->mobile = $mobile;

            $this->askComplaint();
        });
    }

    protected function askComplaint(): void {
        $this->ask(__('chat.complaint.ask_text'), function (Answer $answer) {
            $this->complaint = trim($answer->getText());

            $this->storeComplaint();
        });
    }

    protected function storeComplaint(): void {
        Complaint::create([
            'branch_id' => $this->branchId,
            'name' => $this->name,
            'email' => $this->email ?? null,
            'mobile' => $this->mobile,
            'description' => $this->complaint,
            'ip_address' => request()->ip(),
            'status' => 'pending',
            'blocked' => false,
        ]);

        $this->say(__('chat.complaint.success'));
    }

    /**
     * Basic phone/mobile validation: at least 8 digits.
     */
    protected function isValidPhone(string $value): bool {
        $digits = preg_replace('/\D+/', '', $value);

        return strlen($digits) >= 8;
    }
}


