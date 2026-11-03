<?php

namespace App\Livewire\Admin;

use App\Models\SupportBot;
use App\Services\GrokSupportBot;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Bots extends Component
{
    public ?int $editingBotId = null;
    public bool $showForm = false;

    public string $name = '';
    public string $description = '';
    public string $task = SupportBot::TASK_SUPPORT_CHAT;
    public string $systemPrompt = '';
    public string $knowledge = '';
    public string $model = 'grok-4-fast';
    public bool $isActive = true;

    public string $testInput = '';
    public string $testOutput = '';
    public ?int $testingBotId = null;

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);
    }

    protected function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:120'],
            'description'  => ['nullable', 'string', 'max:255'],
            'task'         => ['required', 'string', 'in:' . implode(',', array_keys(SupportBot::TASKS))],
            'systemPrompt' => ['required', 'string', 'max:6000'],
            'knowledge'    => ['nullable', 'string', 'max:60000'],
            'model'        => ['required', 'string', 'max:64'],
        ];
    }

    public function newBot(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editBot(int $botId): void
    {
        $bot = SupportBot::find($botId);
        if (!$bot) return;

        $this->editingBotId = $bot->id;
        $this->name         = $bot->name;
        $this->description  = (string) $bot->description;
        $this->task         = $bot->task;
        $this->systemPrompt = $bot->system_prompt;
        $this->knowledge    = (string) $bot->knowledge;
        $this->model        = $bot->model;
        $this->isActive     = $bot->is_active;
        $this->showForm     = true;
    }

    public function saveBot(): void
    {
        $this->validate();

        $data = [
            'name'          => $this->name,
            'description'   => $this->description ?: null,
            'task'          => $this->task,
            'system_prompt' => $this->systemPrompt,
            'knowledge'     => $this->knowledge ?: null,
            'model'         => $this->model,
            'is_active'     => $this->isActive,
        ];

        if ($this->editingBotId) {
            SupportBot::whereKey($this->editingBotId)->update($data);
        } else {
            $data['created_by'] = auth()->id();
            SupportBot::create($data);
        }

        session()->flash('bots-success', $this->editingBotId ? 'Bot updated.' : 'Bot created.');
        $this->resetForm();
        $this->showForm = false;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $botId): void
    {
        $bot = SupportBot::find($botId);
        if (!$bot) return;
        $bot->update(['is_active' => !$bot->is_active]);
    }

    public function deleteBot(int $botId): void
    {
        SupportBot::whereKey($botId)->delete();
        session()->flash('bots-success', 'Bot deleted.');
    }

    public function runTest(int $botId, GrokSupportBot $grok): void
    {
        $this->testingBotId = $botId;
        $this->testOutput = '';
        $input = trim($this->testInput);
        if ($input === '') {
            $this->testOutput = 'Type a sample customer message first.';
            return;
        }

        $bot = SupportBot::find($botId);
        if (!$bot) {
            $this->testOutput = 'Bot not found.';
            return;
        }

        try {
            $this->testOutput = $grok->test($bot, $input);
        } catch (\Throwable $e) {
            $this->testOutput = 'Error: ' . $e->getMessage();
        }
    }

    private function resetForm(): void
    {
        $this->editingBotId = null;
        $this->name         = '';
        $this->description  = '';
        $this->task         = SupportBot::TASK_SUPPORT_CHAT;
        $this->systemPrompt = '';
        $this->knowledge    = '';
        $this->model        = (string) config('services.grok.model', 'grok-4-fast');
        $this->isActive     = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.bots', [
            'bots'          => SupportBot::orderByDesc('id')->get(),
            'tasks'         => SupportBot::TASKS,
            'grokConfigured'=> !empty(config('services.grok.api_key')),
        ])->layout('admin.layout.master-slot');
    }
}
