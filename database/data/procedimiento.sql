DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Sp_Rpte_Stock_fecha`(fecini Date,fecfin date)
BEGIN
SELECT p.id, p.nombre,cat.descripcion as categoria,ma.marca,tbl.descripcion as medida,
(
    ifnull((SELECT sum(ddc.cantidad) from compra_documento_detalles ddc INNER JOIN compra_documentos dc ON ddc.documento_id = dc.id WHERE dc.fecha_emision < fecini AND ddc.producto_id = p.id AND dc.estado != 'ANULADO'),0) + 
    ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha < fecini AND dni.producto_id = p.id AND ni.estado != 'ANULADO'),0) -
    ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < fecini AND dv.estado != 'ANULADO' AND lp.producto_id = p.id AND ddv.eliminado = '0'),0) +
    ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < fecini AND dv.estado != 'ANULADO' AND lp.producto_id = p.id AND dv.tipo_venta != '129' AND dv.convertir != '' AND ddv.eliminado = '0'),0) -
    ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha < fecini AND ns.estado != 'ANULADO' AND dns.producto_id = p.id),0) + 
    ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision < fecini AND ne.estado != 'ANULADO' AND lpn.producto_id = p.id),0)
) as STOCKINI,
ifnull((SELECT SUM(cdd.cantidad) from compra_documento_detalles cdd INNER JOIN compra_documentos cd ON cdd.documento_id = cd.id WHERE cd.fecha_emision >= fecini AND cd.fecha_emision <= fecfin AND cd.estado != 'ANULADO' AND cdd.producto_id = p.id),0) AS COMPRAS,
ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha >= fecini AND ni.fecha <= fecfin AND dni.producto_id = p.id AND ni.estado != 'ANULADO'),0) AS INGRESOS,
ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision >= fecini AND ne.fechaEmision <= fecfin AND  ne.estado != 'ANULADO' AND lpn.producto_id = p.id),0) as DEVOLUCIONES,
(
    ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= fecini and vd.fecha_documento <= fecfin AND vd.estado != 'ANULADO' AND lp.producto_id = p.id AND vdd.eliminado = '0'),0) -
    ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= fecini and vd.fecha_documento <= fecfin AND vd.estado != 'ANULADO' AND lp.producto_id = p.id AND vd.tipo_venta != '129' AND vd.convertir != '' AND vdd.eliminado = '0'),0)
) AS VENTAS,
ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha >= fecini AND ns.fecha <= fecfin AND ns.estado != 'ANULADO' AND dns.producto_id = p.id),0) AS SALIDAS,
(
    (
        ifnull((SELECT sum(ddc.cantidad) from compra_documento_detalles ddc INNER JOIN compra_documentos dc ON ddc.documento_id = dc.id WHERE dc.fecha_emision < fecini AND ddc.producto_id = p.id AND dc.estado != 'ANULADO'),0) +
        ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha < fecini AND dni.producto_id = p.id AND ni.estado != 'ANULADO'),0) -
        ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < fecini AND dv.estado != 'ANULADO' AND lp.producto_id = p.id AND ddv.eliminado = '0'),0) +
        ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < fecini AND dv.estado != 'ANULADO' AND lp.producto_id = p.id AND dv.tipo_venta != '129' AND dv.convertir != '' AND ddv.eliminado = '0'),0) -
        ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha < fecini AND ns.estado != 'ANULADO' AND dns.producto_id = p.id),0) +
        ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision < fecini AND ne.estado != 'ANULADO' AND lpn.producto_id = p.id),0)
    ) -
    (
        ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= fecini and vd.fecha_documento <= fecfin AND vd.estado != 'ANULADO' AND lp.producto_id = p.id AND vdd.eliminado = '0'),0) -
        ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= fecini and vd.fecha_documento <= fecfin AND vd.estado != 'ANULADO' AND lp.producto_id = p.id AND vd.tipo_venta != '129' AND vd.convertir != '' AND vdd.eliminado = '0'),0) +
        ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha >= fecini AND ns.fecha <= fecfin AND ns.estado != 'ANULADO' AND dns.producto_id = p.id),0)
    ) +
    ifnull((SELECT SUM(cdd.cantidad) from compra_documento_detalles cdd INNER JOIN compra_documentos cd ON cdd.documento_id = cd.id WHERE cd.fecha_emision >= fecini AND cd.fecha_emision <= fecfin AND cd.estado != 'ANULADO' AND cdd.producto_id = p.id),0) +
    ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha >= fecini AND ni.fecha <= fecfin AND dni.producto_id = p.id AND ni.estado != 'ANULADO'),0) +
    ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision >= fecini AND ne.fechaEmision <= fecfin AND  ne.estado != 'ANULADO' AND lpn.producto_id = p.id),0)
) as STOCK,
fecini as fecini, fecfin as fecfin
from productos p
inner join categorias cat
on cat.id = p.categoria_id
inner join marcas ma
on ma.id = p.marca_id
inner join tabladetalles tbl
on tbl.id = p.medida
order by STOCK desc;
END$$
