<label class="form__field"><span>SKU</span><input class="input" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required></label>
<label class="form__field"><span>Nombre</span><input class="input" name="name" value="{{ old('name', $product->name ?? '') }}" required></label>
<label class="form__field"><span>Categoría</span><select class="input" name="category_id"><option value="">Sin categoría</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></label>
<label class="form__field"><span>Unidad</span><input class="input" name="unit" value="{{ old('unit', $product->unit ?? 'unidad') }}" required></label>
<label class="form__field"><span>Precio venta</span><input class="input" type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? 0) }}"></label>
<label class="form__field"><span>Precio costo</span><input class="input" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}"></label>
<label class="form__field"><span>Impuesto (%)</span><input class="input" type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 0) }}"></label>
<label class="form__field"><span>Stock mínimo</span><input class="input" type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 0) }}"></label>
<label><input type="checkbox" name="is_service" value="1" @checked(old('is_service', $product->is_service ?? false))> Es servicio (no descuenta stock)</label>
