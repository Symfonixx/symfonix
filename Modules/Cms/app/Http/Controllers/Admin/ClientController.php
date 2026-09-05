<?php

namespace Modules\Cms\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cms\Data\ClientData;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Client;
use Modules\Cms\Repositories\Client\ClientRepository;
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
        $maxRank = Client::max('rank') ?? 0;
        $minRank = max($maxRank, 1);

        return view('cms::admin.client.create', compact('minRank'));
    }

    public function store(Request $request): RedirectResponse
    {
        $maxRank = Client::max('rank') ?? 0;
        $minRank = max($maxRank, 1);
        $rank = (int) $request->input('rank', $minRank);

        if ($rank < $minRank) {
            return back()->withErrors(['rank' => __('Rank must be at least :min', ['min' => $minRank])])->withInput();
        }

        $data = ClientData::validate([
            'name' => $request->input('name'),
            'logo' => $request->file('logo'),
            'url' => $request->input('url') ?: null,
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');
        $this->clientRepository->store($data);

        return redirect()->route('admin.clients.index');
    }

    public function edit(Client $client)
    {
        $maxRank = Client::where('id', '!=', $client->id)->max('rank') ?? 0;
        $minRank = max($maxRank, 1);

        return view('cms::admin.client.edit', compact('client', 'minRank'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $maxRank = Client::where('id', '!=', $client->id)->max('rank') ?? 0;
        $minRank = max($maxRank, 1);
        $rank = (int) $request->input('rank', $client->rank);

        if ($rank < min($client->rank, $minRank)) {
            return back()->withErrors(['rank' => __('Rank must be at least :min', ['min' => min($client->rank, $minRank)])])->withInput();
        }

        $data = ClientData::validate([
            'name' => $request->input('name'),
            'logo' => $request->file('logo'),
            'url' => $request->input('url') ?: null,
            'rank' => $rank,
            'status' => $request->has('publish') ? CmsStatus::PUBLISHED : CmsStatus::ARCHIVED,
        ]);
        $data['auto_translate'] = $request->boolean('auto_translate');
        $this->clientRepository->update($data, $client);

        return redirect()->route('admin.clients.index');
    }

    public function deleteMulti(DeleteMultiRequest $request): RedirectResponse
    {
        $this->clientRepository->deleteMulti($request->input('ids'));

        return back();
    }
}
