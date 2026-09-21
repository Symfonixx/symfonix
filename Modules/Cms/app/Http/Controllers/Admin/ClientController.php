<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Data\ClientData;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Client;
use Modules\Cms\Repositories\Client\ClientRepository;
use Modules\Cms\Support\RankConstraint;
use Modules\Core\Http\Requests\DeleteMultiRequest;

class ClientController extends Controller
{
    protected ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->setActive('cms');
        $this->setActive('clients');
    }

    public function index()
    {
        $model = $this->clientRepository->all([
            'id', 'name', 'logo', 'url', 'rank', 'status', 'created_at',
        ]);

        return view('cms::admin.client.index', compact('model'));
    }

    public function create()
    {
        $minRank = RankConstraint::minRank(Client::class);

        return view('cms::admin.client.create', compact('minRank'));
    }

    public function store(Request $request): RedirectResponse
    {
        $minRank = RankConstraint::minRank(Client::class);
        $rank = (int) $request->input('rank', $minRank);

        if ($redirect = RankConstraint::rejectIfBelow($rank, $minRank)) {
            return $redirect;
        }

        $this->clientRepository->store($this->payload($request, $rank));

        return redirect()->route('admin.clients.index');
    }

    public function edit(Client $client)
    {
        $minRank = RankConstraint::minRank(Client::class, $client->id);

        return view('cms::admin.client.edit', compact('client', 'minRank'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $minRank = RankConstraint::minRank(Client::class, $client->id);
        $rank = (int) $request->input('rank', $client->rank);

        if ($redirect = RankConstraint::rejectIfBelow($rank, min((int) $client->rank, $minRank))) {
            return $redirect;
        }

        $this->clientRepository->update($this->payload($request, $rank), $client);

        return redirect()->route('admin.clients.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->clientRepository->deleteMulti($request->input('ids'));

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, int $rank): array
    {
        $data = ClientData::validate([
            'name' => $request->input('name'),
            'logo' => $request->file('logo'),
            'url' => $request->input('url') ?: null,
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');

        return $data;
    }
}
