<table class="table table-bordered table-hover mb-0" id="tbl-permissions">

    <thead>
        <tr>
            <th width="60" class="text-center align-middle">
                <i class="fas fa-check-square"></i>
            </th>

            <th width="80" class="align-middle">
                ID
            </th>

            <th class="align-middle">
                <i class="fas fa-layer-group mr-1"></i>
                MÓDULO
            </th>

            <th class="align-middle">
                <i class="fas fa-puzzle-piece mr-1"></i>
                SUBMÓDULO
            </th>

            <th class="align-middle">
                <i class="fas fa-link mr-1"></i>
                SLUG
            </th>

            <th class="align-middle">
                <i class="fas fa-key mr-1"></i>
                PERMISO
            </th>
        </tr>
    </thead>

    <tbody>

        @foreach ($permissions as $permission)
            @php
                $parts = explode('.', $permission->slug);

                $modulo = $parts[0] ?? $permission->slug;
                $submodulo = $parts[1] ?? '-';
            @endphp

            <tr>

                {{-- CHECKBOX --}}
                <td class="text-center align-middle">

                    <div class="custom-control custom-checkbox">

                        <input type="checkbox" class="custom-control-input permission-checkbox"
                            id="permission_{{ $permission->id }}" data-id="{{ $permission->id }}" name="permissions[]"
                            value="{{ $permission->id }}"
                            {{ in_array($permission->id, $permission_role ?? []) ? 'checked' : '' }}>

                        <label class="custom-control-label" for="permission_{{ $permission->id }}">
                        </label>

                    </div>

                </td>

                {{-- ID --}}
                <td class="align-middle font-weight-bold text-secondary">
                    {{ $permission->id }}
                </td>

                {{-- MODULO --}}
                <td class="align-middle">

                    <span class="badge badge-success px-3 py-2 shadow-sm">
                        <i class="fas fa-folder-open mr-1"></i>
                        {{ strtoupper($modulo) }}
                    </span>

                </td>

                {{-- SUBMODULO --}}
                <td class="align-middle">

                    <span class="badge badge-info px-3 py-2 shadow-sm">
                        <i class="fas fa-cube mr-1"></i>
                        {{ strtoupper($submodulo) }}
                    </span>

                </td>

                {{-- SLUG --}}
                <td class="align-middle">
                    <code class="bg-light px-2 py-1 rounded text-success">
                        {{ $permission->slug }}
                    </code>
                </td>

                {{-- PERMISO --}}
                <td class="align-middle font-weight-medium">
                    {{ $permission->name }}
                </td>

            </tr>
        @endforeach

    </tbody>

</table>
